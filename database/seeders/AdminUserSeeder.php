<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Administrator',
            'username' => 'Admin ARSIPIN',
            'email' => 'admin@arsipin.id',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role_type' => 'admin',
        ]);
        $admin->assignRole('admin');

        // Create Staff User (Pegawai TU)
        $staff = User::create([
            'name' => 'Siti Nurhaliza',
            'username' => 'Staff ARSIPIN',
            'email' => 'staff@arsipin.id',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role_type' => 'staff',
        ]);
        $staff->assignRole('staff');

        // Create Intern User (Mahasiswa Magang)
        $intern = User::create([
            'name' => 'Ahmad Firmansyah',
            'username' => 'Intern ARSIPIN',
            'email' => 'intern@arsipin.id',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role_type' => 'intern',
        ]);
        $intern->assignRole('intern');

        $this->command->info('Demo users created successfully!');
        $this->command->table(['Name', 'Username', 'Email', 'Role', 'Password'], [
            ['Administrator', 'Admin ARSIPIN', 'admin@arsipin.id', 'Admin', 'password'],
            ['Siti Nurhaliza', 'Staff ARSIPIN', 'staff@arsipin.id', 'Pegawai TU', 'password'],
            ['Ahmad Firmansyah', 'Intern ARSIPIN', 'intern@arsipin.id', 'Mahasiswa Magang', 'password'],
        ]);
    }
}
