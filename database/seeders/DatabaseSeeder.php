<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            RolesAndPermissionsSeeder::class, // Run this first
            AdminUserSeeder::class, // Then create users with roles
        ]);

        // Only run fake data seeder in local/testing environment
        // if (app()->environment(['local', 'testing'])) {
        //     $this->call([
        //         FakeDataSeeder::class, // Generate fake data for testing
        //     ]);
        // }
    }
}
