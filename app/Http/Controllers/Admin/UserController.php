<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Show user details
     */
    public function show(User $user)
    {
        $user->load('roles');
        return view('admin.users.show', compact('user'));
    }
    
    /**
     * Show edit user form
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $user->load('roles');
        return view('admin.users.edit', compact('user', 'roles'));
    }
    
    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,name'
        ]);
        
        try {
            DB::beginTransaction();
            
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
            ];
            
            // Only update password if provided
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }
            
            $user->update($updateData);
            
            // Sync roles
            $user->syncRoles([$request->role]);
            
            DB::commit();
            
            Log::info("User updated: {$user->name} ({$user->email}) role changed to {$request->role} by " . auth()->id());
            
            return redirect()->route('admin.roles.index')
                ->with('success', "User '{$user->name}' berhasil diperbarui!");
                
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('User update error: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat memperbarui user: ' . $e->getMessage()]);
        }
    }

    /**
     * Show create user form
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }
    
    /**
     * Store new user
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name'
        ]);
        
        try {
            DB::beginTransaction();
            
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(), // Auto verify
            ]);
            
            $user->assignRole($request->role);
            
            DB::commit();
            
            Log::info("User created: {$user->name} ({$user->email}) with role {$request->role} by " . auth()->id());
            
            return redirect()->route('admin.roles.index')
                ->with('success', "User '{$user->name}' berhasil dibuat dengan role {$request->role}!");
                
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('User creation error: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat membuat user: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        // Prevent deleting current user
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menghapus akun sendiri!'
            ], 400);
        }
        
        // Prevent deleting admin users (optional protection)
        if ($user->hasRole('admin') && User::role('admin')->count() <= 1) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus admin terakhir!'
            ], 400);
        }
        
        try {
            $userName = $user->name;
            $user->delete();
            
            Log::info("User deleted: {$userName} by " . auth()->id());
            
            return response()->json([
                'success' => true,
                'message' => "User '{$userName}' berhasil dihapus!"
            ]);
            
        } catch (\Exception $e) {
            Log::error('User deletion error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus user: ' . $e->getMessage()
            ], 500);
        }
    }
} 