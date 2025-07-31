<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UpdateRolePermissions extends Command
{
    protected $signature = 'update:role-permissions';
    protected $description = 'Update role permissions for Admin, TU, and Magang';

    public function handle()
    {
        $this->info('🔧 UPDATING ROLE PERMISSIONS...');

        // 1. Create permissions
        $this->info('📋 1. Creating permissions...');

        $permissions = [
            // Dashboard
            'dashboard.view',

            // Archive Management
            'archives.view',
            'archives.create',
            'archives.edit',
            'archives.delete',
            'archives.export',

            // Storage Management
            'storage.view',
            'storage.create',
            'storage.edit',
            'storage.delete',
            'storage.set-location',

            // Re-evaluation
            're-evaluation.view',
            're-evaluation.update-status',
            're-evaluation.bulk-update',

            // Bulk Operations
            'bulk.view',
            'bulk.status-change',
            'bulk.export',
            'bulk.move-storage',
            'bulk.delete',

            // Reports
            'reports.view',
            'reports.retention',

            // User Management (Admin only)
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // Role Management (Admin only)
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

            // Analytics (Admin only)
            'analytics.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
            $this->info("   ✅ Created permission: {$permission}");
        }

        // 2. Create/Update Roles
        $this->info('📋 2. Creating/Updating roles...');

        // Admin Role - Full access
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions($permissions);
        $this->info("   ✅ Admin role updated with all permissions");

        // TU Role - Limited access
        $tuRole = Role::firstOrCreate(['name' => 'staff']);
        $tuPermissions = [
            'dashboard.view',
            'archives.view',
            'archives.create',
            'archives.edit',
            'archives.export',
            'storage.view',
            'storage.create',
            'storage.edit',
            'storage.set-location',
            'bulk.view',
            'bulk.status-change',
            'bulk.export',
            'bulk.move-storage',
            'reports.view',
            'reports.retention',
        ];
        $tuRole->syncPermissions($tuPermissions);
        $this->info("   ✅ TU role updated with limited permissions");

        // Magang Role - Basic access
        $magangRole = Role::firstOrCreate(['name' => 'intern']);
        $magangPermissions = [
            'dashboard.view',
            'archives.view',
            'archives.create',
            'archives.edit',
            'archives.export',
            'storage.set-location',
        ];
        $magangRole->syncPermissions($magangPermissions);
        $this->info("   ✅ Magang role updated with basic permissions");

        // 3. Update existing users
        $this->info('📋 3. Updating existing users...');

        $users = \App\Models\User::all();
        foreach ($users as $user) {
            if (!$user->hasAnyRole(['admin', 'staff', 'intern'])) {
                // Assign default role based on role_type
                switch ($user->role_type) {
                    case 'admin':
                        $user->assignRole('admin');
                        $this->info("   ✅ User {$user->name} assigned admin role");
                        break;
                    case 'staff':
                        $user->assignRole('staff');
                        $this->info("   ✅ User {$user->name} assigned staff role");
                        break;
                    case 'intern':
                        $user->assignRole('intern');
                        $this->info("   ✅ User {$user->name} assigned intern role");
                        break;
                    default:
                        $user->assignRole('intern');
                        $this->info("   ✅ User {$user->name} assigned default intern role");
                        break;
                }
            }
        }

        $this->info('✅ ROLE PERMISSIONS UPDATED SUCCESSFULLY!');
        $this->info('');
        $this->info('📝 PERMISSION SUMMARY:');
        $this->info('ADMIN: Full access to all features');
        $this->info('TU: Dashboard, Archive Management (CRUD), Storage Management (CRUD), Bulk Operations, Reports');
        $this->info('MAGANG: Dashboard, Archive Management (CRUD), Set Location');
    }
}
