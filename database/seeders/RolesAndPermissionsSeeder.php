<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions based on actual application features
        $permissions = [
            // Archive management permissions
            'view archives',
            'create archives',
            'edit archives',
            'delete archives',
            'export archives',
            'bulk operations',
            'print labels',
            'evaluate archives',
            'destroy archives',

            // Storage management permissions
            'manage storage',
            'view storage',
            'create storage',
            'edit storage',
            'delete storage',

            // Master data permissions
            'manage categories',
            'manage classifications',
            'manage locations',
            'manage related documents',

            // User management permissions
            'manage users',
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Role management permissions
            'manage roles',
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',

            // Dashboard permissions
            'view admin dashboard',
            'view staff dashboard',
            'view intern dashboard',
            'view analytics dashboard',

            // Search permissions
            'search archives',
            'advanced search',

            // Telegram bot permissions
            'manage telegram bot',
            'view telegram logs',

            // System permissions
            'view system logs',
            'manage system settings',
        ];

        // Create permissions safely (won't duplicate if they exist)
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles safely (won't duplicate if they exist)

        // 1. ADMIN ROLE - Full access to everything
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // 2. STAFF ROLE (Pegawai TU) - Most archive operations + analytics
        $staffRole = Role::firstOrCreate(['name' => 'staff']);
        $staffRole->givePermissionTo([
            'view archives',
            'create archives',
            'edit archives',
            'export archives',
            'bulk operations',
            'print labels',
            'evaluate archives',
            'destroy archives',
            'view storage',
            'manage storage',
            'view staff dashboard',
            'view analytics dashboard',
            'search archives',
            'advanced search',
        ]);

        // 3. INTERN ROLE (Mahasiswa Magang) - Basic archive operations only
        $internRole = Role::firstOrCreate(['name' => 'intern']);
        $internRole->givePermissionTo([
            'view archives',
            'create archives',
            'edit archives',
            'export archives',
            'print labels',
            'view storage',
            'view intern dashboard',
            'search archives',
        ]);

        $this->command->info('Roles and permissions created/updated successfully!');
        $this->command->table(['Role', 'Permissions'], [
            ['admin', 'Full access to all features and system management'],
            ['staff', 'Archive management + Storage + Analytics Dashboard'],
            ['intern', 'Basic archive operations + View access only'],
        ]);
    }
}
