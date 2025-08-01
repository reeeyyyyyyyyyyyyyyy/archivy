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

        // Create permissions based on documentation
        $permissions = [
            // Archive permissions
            'view archives',
            'create archives', 
            'edit archives',
            'delete archives',
            'export archives',
            
            // Master data permissions
            'manage categories',
            'manage classifications',
            
            // Dashboard permissions
            'view admin dashboard',
            'view analytics dashboard',
            
            // Role management permissions
            'manage roles',
            'manage users',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // 1. ADMIN ROLE - Full access
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // 2. STAFF ROLE (Pegawai TU) - CRUD Archives, view masters, analytics dashboard  
        $staffRole = Role::create(['name' => 'staff']);
        $staffRole->givePermissionTo([
            'view archives',
            'create archives',
            'edit archives',
            'export archives',
            'view admin dashboard',
            'view analytics dashboard',
        ]);

        // 3. INTERN ROLE (Mahasiswa Magang) - View, create, edit archives, export only
        $internRole = Role::create(['name' => 'intern']);
        $internRole->givePermissionTo([
            'view archives',
            'create archives',
            'edit archives',
            'export archives',
            'view admin dashboard', // Basic dashboard only, no analytics
        ]);

        $this->command->info('Roles and permissions created successfully!');
        $this->command->table(['Role', 'Permissions'], [
            ['admin', 'Full access to all features'],
            ['staff', 'CRUD Archives + Analytics Dashboard'],
            ['intern', 'CRUD Archives + Basic Dashboard only'],
        ]);
    }
}
