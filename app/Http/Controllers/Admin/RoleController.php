<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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

            Log::info("Role created: {$role->name} by " . Auth::id());

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
        $permissions = Permission::all();

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update role
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        try {
            DB::beginTransaction();

            // Sync permissions by IDs
            if ($request->has('permissions')) {
                $role->permissions()->sync($request->permissions);
            } else {
                $role->permissions()->sync([]);
            }

            DB::commit();

            Log::info("Role permissions updated: {$role->name} by user " . Auth::id());

            return redirect()->route('admin.roles.index')
                ->with('success', "Permissions untuk role '{$role->name}' berhasil diperbarui!");

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

            // Remove all existing roles first, then assign new role
            $user->syncRoles([$role->name]);

            Log::info("Role '{$role->name}' assigned to user '{$user->name}' by " . Auth::id());

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

            Log::info("Role '{$role->name}' removed from user '{$user->name}' by " . Auth::id());

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
     * Get users with specific role (for delete confirmation)
     */
    public function getRoleUsers(Role $role)
    {
        $users = $role->users()->select('id', 'name', 'email')->get();

        return response()->json([
            'success' => true,
            'users' => $users,
            'count' => $users->count()
        ]);
    }

    /**
     * Get user's roles for removal selection
     */
    public function getUserRoles(User $user)
    {
        $roles = $user->roles()->withCount('permissions')->select('roles.id', 'roles.name')->get();

        return response()->json([
            'success' => true,
            'roles' => $roles,
            'count' => $roles->count()
        ]);
    }

    /**
     * Remove specific roles from user
     */
    public function removeUserRoles(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_ids' => 'required|array|min:1',
            'role_ids.*' => 'exists:roles,id'
        ]);

        $userId = $request->user_id;
        $roleIds = $request->role_ids;
        $user = User::findOrFail($userId);
        $successCount = 0;
        $removedRoles = [];

        DB::beginTransaction();
        try {
            foreach ($roleIds as $roleId) {
                $role = Role::find($roleId);
                if ($role && $user->hasRole($role)) {
                    $user->removeRole($role);
                    $successCount++;
                    $removedRoles[] = $role->name;

                    Log::info("Role '{$role->name}' removed from user '{$user->name}' by " . Auth::id());
                }
            }

            DB::commit();

            $message = "Berhasil menghapus {$successCount} role dari user '{$user->name}': " . implode(', ', $removedRoles);

            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $successCount,
                'removed_roles' => $removedRoles
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Remove user roles error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus role: ' . $e->getMessage()
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
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role sistem tidak dapat dihapus!'
                ], 400);
            }
            return redirect()->back()
                ->withErrors(['error' => 'Role sistem tidak dapat dihapus!']);
        }

        try {
            $roleName = $role->name;

            // Remove role from all users first
            $role->users()->detach();

            // Delete the role
            $role->delete();

            Log::info("Role deleted: {$roleName} by user " . Auth::id());

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Role '{$roleName}' berhasil dihapus!"
                ]);
            }

            return redirect()->route('admin.roles.index')
                ->with('success', "Role '{$roleName}' berhasil dihapus!");

        } catch (\Exception $e) {
            Log::error('Role deletion error: ' . $e->getMessage());

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menghapus role: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat menghapus role: ' . $e->getMessage()]);
        }
    }

    /**
     * Bulk delete roles with user selection
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'role_ids' => 'required|array|min:1',
            'role_ids.*' => 'exists:roles,id',
            'affected_users' => 'array',
            'affected_users.*' => 'exists:users,id'
        ]);

        $roleIds = $request->role_ids;
        $affectedUsers = $request->affected_users ?? [];
        $successCount = 0;
        $errors = [];
        $deletedRoles = [];

        DB::beginTransaction();
        try {
            foreach ($roleIds as $roleId) {
                $role = Role::find($roleId);

                // Prevent deletion of core roles
                if (in_array($role->name, ['admin', 'staff', 'intern'])) {
                    $errors[] = "Role '{$role->name}' tidak dapat dihapus karena merupakan role sistem";
                    continue;
                }

                // Remove role from all users first
                $role->users()->detach();

                // Delete the role
                $roleName = $role->name;
                $role->delete();
                $successCount++;
                $deletedRoles[] = $roleName;

                Log::info("Role deleted in bulk: {$roleName} by user " . Auth::id());
            }

            DB::commit();

            $message = "Berhasil menghapus {$successCount} role: " . implode(', ', $deletedRoles);
            if (!empty($errors)) {
                $message .= ". Beberapa role tidak dapat dihapus: " . implode(', ', $errors);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $successCount,
                'deleted_roles' => $deletedRoles,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Bulk role deletion error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk remove roles from users
     */
    public function bulkRemoveUsers(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id'
        ]);

        $userIds = $request->user_ids;
        $successCount = 0;
        $errors = [];
        $affectedUsers = [];

        DB::beginTransaction();
        try {
            foreach ($userIds as $userId) {
                $user = User::find($userId);

                if (!$user) {
                    $errors[] = "User dengan ID {$userId} tidak ditemukan";
                    continue;
                }

                // Get user's roles before removal for logging
                $userRoles = $user->roles->pluck('name')->toArray();

                // Remove all roles from user
                $user->roles()->detach();
                $successCount++;
                $affectedUsers[] = $user->name;

                Log::info("Roles removed from user '{$user->name}' (ID: {$userId}): " . implode(', ', $userRoles) . " by user " . Auth::id());
            }

            DB::commit();

            $message = "Berhasil menghapus role dari {$successCount} user: " . implode(', ', $affectedUsers);
            if (!empty($errors)) {
                $message .= ". Beberapa user tidak dapat diproses: " . implode(', ', $errors);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $successCount,
                'affected_users' => $affectedUsers,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Bulk remove users from roles error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus role dari user: ' . $e->getMessage()
            ], 500);
        }
    }
}
