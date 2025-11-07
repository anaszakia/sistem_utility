<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create comprehensive permissions for full system management
        $permissions = [
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Role & Permission Management
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',
            'assign roles',
            'assign permissions',
            
            // Dashboard Access
            'access admin dashboard',
            'access user dashboard',
            
            // Profile Management
            'edit profile',
            'view profile',
            
            // Audit & Logs
            'view audit logs',
            'export audit logs',
            'delete audit logs',
            
            // System Settings
            'manage settings',
            'view reports',
            'export reports',
            
            // Repair Request Management
            'view repair requests',
            'create repair requests',
            'edit repair requests',
            'delete repair requests',
            'approve repair requests',
            'reject repair requests',
            'complete repair requests',
            
            // Schedule Management
            'view schedules',
            'create schedules',
            'edit schedules',
            'delete schedules',
            
            // Technician Assignment
            'view assignments',
            'assign technicians',
            'edit assignments',
            'delete assignments',
            
            // Technician Work
            'view my assignments',
            'start work',
            'complete work',
            'add work notes',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Super Admin role with ALL permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // Create Admin Utility role
        $adminUtilityRole = Role::firstOrCreate(['name' => 'admin_utility']);
        $adminUtilityRole->givePermissionTo([
            'view users',
            'create users',
            'edit users',
            'access admin dashboard',
            'view profile',
            'edit profile',
            'view audit logs',
            'export audit logs',
            'view reports',
            'export reports',
            // Repair Request permissions
            'view repair requests',
            'edit repair requests',
            'approve repair requests',
            'reject repair requests',
            'complete repair requests',
            // Schedule permissions
            'view schedules',
            'create schedules',
            'edit schedules',
            'delete schedules',
            // Assignment permissions
            'view assignments',
            'assign technicians',
            'edit assignments',
            'delete assignments',
        ]);

        // Create Admin Departemen role
        $adminDepartemenRole = Role::firstOrCreate(['name' => 'admin_departemen']);
        $adminDepartemenRole->givePermissionTo([
            'view users',
            'access admin dashboard',
            'view profile',
            'edit profile',
            'view audit logs',
            'view reports',
            // Repair Request permissions
            'view repair requests',
            'create repair requests',
            'edit repair requests',
        ]);

        // Create Teknisi Utility role
        $teknisiUtilityRole = Role::firstOrCreate(['name' => 'teknisi_utility']);
        $teknisiUtilityRole->givePermissionTo([
            'access user dashboard',
            'view profile',
            'edit profile',
            // Technician Work permissions
            'view my assignments',
            'start work',
            'complete work',
            'add work notes',
        ]);

        $this->command->info('Super Admin role created with all permissions.');
        $this->command->info('Admin Utility role created.');
        $this->command->info('Admin Departemen role created.');
        $this->command->info('Teknisi Utility role created.');
        $this->command->info('Total permissions created: ' . count($permissions));
    }
}
