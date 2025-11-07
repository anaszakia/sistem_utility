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
        // Run role and permission seeder first
        $this->call(RolePermissionSeeder::class);

        // Create Super Admin user
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole('super_admin');

        // Create Admin Utility user
        $adminUtility = User::create([
            'name' => 'Admin Utility',
            'email' => 'adminutility@gmail.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);
        $adminUtility->assignRole('admin_utility');

        // Create Admin Departemen user
        $adminDepartemen = User::create([
            'name' => 'Admin Departemen',
            'email' => 'admindepartemen@gmail.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);
        $adminDepartemen->assignRole('admin_departemen');

        // Create Teknisi Utility user
        $teknisiUtility = User::create([
            'name' => 'Teknisi Utility',
            'email' => 'teknisiutility@gmail.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);
        $teknisiUtility->assignRole('teknisi_utility');

        $this->command->info('Users created successfully:');
        $this->command->info('');
        $this->command->info('1. Super Admin');
        $this->command->info('   Email: superadmin@gmail.com');
        $this->command->info('   Password: password123');
        $this->command->info('   Role: super_admin');
        $this->command->info('');
        $this->command->info('2. Admin Utility');
        $this->command->info('   Email: adminutility@gmail.com');
        $this->command->info('   Password: password123');
        $this->command->info('   Role: admin_utility');
        $this->command->info('');
        $this->command->info('3. Admin Departemen');
        $this->command->info('   Email: admindepartemen@gmail.com');
        $this->command->info('   Password: password123');
        $this->command->info('   Role: admin_departemen');
        $this->command->info('');
        $this->command->info('4. Teknisi Utility');
        $this->command->info('   Email: teknisiutility@gmail.com');
        $this->command->info('   Password: password123');
        $this->command->info('   Role: teknisi_utility');
    }
}
