<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display role list
     */
    public function index()
    {
        $roles = Role::withCount(['users', 'permissions'])->get();
        $permissions = Permission::all()->groupBy(function($permission) {
            return explode('.', $permission->name)[0]; // Group by prefix (e.g., 'archives', 'categories')
        });
        $users = User::with('roles')->get();
        
        $roleStats = [
            'total_roles' => $roles->count(),
            'total_permissions' => Permission::count(),
            'total_users' => User::count(),
            'users_with_roles' => User::whereHas('roles')->count(),
        ];
        
        return view('admin.roles.index', compact('roles', 'permissions', 'users', 'roleStats'));
    }
    
    /**
     * Show role details
     */
    public function show(Role $role)
    {
        $role->load(['permissions', 'users']);
        return view('admin.roles.show', compact('role'));
    }
    
    /**
     * Show create role form
     */
    public function create()
    {
        $permissions = Permission::all();
        return view('admin.roles.create', compact('permissions'));
    }
    
    /**
     * Store new role
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);
        
        try {
            DB::beginTransaction();
            
            $role = Role::create([
                'name' => strtolower($request->name),
                'guard_name' => 'web'
            ]);
            
            if ($request->has('permissions')) {
                $role->permissions()->sync($request->permissions);
            }
            
            DB::commit();
            
            Log::info("Role created: {$role->name} by " . auth()->id());
            
            return redirect()->route('admin.roles.index')
                ->with('success', "Role '{$role->name}' berhasil dibuat!");
                
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Role creation error: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat membuat role: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Show edit role form
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all()->groupBy(function($permission) {
            return explode('.', $permission->name)[0];
        });
        
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }
    
    /**
     * Update role
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:500',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);
        
        try {
            DB::beginTransaction();
            
            $role->update([
                'name' => $request->name
            ]);
            
            $role->syncPermissions($request->permissions ?? []);
            
            DB::commit();
            
            Log::info("Role updated: {$role->name} by user " . auth()->id());
            
            return redirect()->route('admin.roles.index')
                ->with('success', "Role '{$role->name}' berhasil diperbarui!");
                
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Role update error: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat memperbarui role: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Assign role to user
     */
    public function assignUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id'
        ]);
        
        try {
            $user = User::findOrFail($request->user_id);
            $role = Role::findOrFail($request->role_id);
            
            $user->assignRole($role);
            
            Log::info("Role '{$role->name}' assigned to user '{$user->name}' by " . auth()->id());
            
            return response()->json([
                'success' => true,
                'message' => "Role '{$role->name}' berhasil diberikan kepada {$user->name}"
            ]);
            
        } catch (\Exception $e) {
            Log::error('Role assignment error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat assign role: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Remove role from user
     */
    public function removeUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id'
        ]);
        
        try {
            $user = User::findOrFail($request->user_id);
            $role = Role::findOrFail($request->role_id);
            
            $user->removeRole($role);
            
            Log::info("Role '{$role->name}' removed from user '{$user->name}' by " . auth()->id());
            
            return response()->json([
                'success' => true,
                'message' => "Role '{$role->name}' berhasil dicabut dari {$user->name}"
            ]);
            
        } catch (\Exception $e) {
            Log::error('Role removal error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencabut role: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete role
     */
    public function destroy(Role $role)
    {
        // Prevent deletion of core roles
        if (in_array($role->name, ['admin', 'staff', 'intern'])) {
            return redirect()->back()
                ->withErrors(['error' => 'Role sistem tidak dapat dihapus!']);
        }
        
        try {
            $roleName = $role->name;
            $role->delete();
            
            Log::info("Role deleted: {$roleName} by user " . auth()->id());
            
            return redirect()->route('admin.roles.index')
                ->with('success', "Role '{$roleName}' berhasil dihapus!");
                
        } catch (\Exception $e) {
            Log::error('Role deletion error: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat menghapus role: ' . $e->getMessage()]);
        }
    }
} 