<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ============================================================
        // CREATE PERMISSIONS
        // ============================================================

        // Role Resource Permissions (Shield auto-generated)
        $rolePermissions = [
            'view_role',
            'view_any_role',
            'create_role',
            'update_role',
            'delete_role',
            'delete_any_role',
        ];

        // Custom Permissions for additional features
        $customPermissions = [
            // Analytics & Reporting
            'view_analytics',
            'export_data',
            'generate_reports',

            // System Management
            'manage_settings',
            'view_logs',
            'manage_users',

            // Notifications
            'send_notifications',
            'view_notifications',

            // Smart City Module Permissions (To be used when resources are created)
            'view_smart_farm',
            'create_smart_farm',
            'update_smart_farm',
            'delete_smart_farm',

            'view_smart_parking',
            'create_smart_parking',
            'update_smart_parking',
            'delete_smart_parking',

            'view_smart_traffic',
            'create_smart_traffic',
            'update_smart_traffic',
            'delete_smart_traffic',

            'view_smart_lighting',
            'create_smart_lighting',
            'update_smart_lighting',
            'delete_smart_lighting',

            'view_fire_alarm',
            'create_fire_alarm',
            'update_fire_alarm',
            'delete_fire_alarm',

            'view_smart_tank',
            'create_smart_tank',
            'update_smart_tank',
            'delete_smart_tank',
        ];

        // Create all permissions
        foreach (array_merge($rolePermissions, $customPermissions) as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ============================================================
        // CREATE ROLES
        // ============================================================

        // 1. SUPER ADMIN - Full access to everything
        $superAdmin = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web'
        ]);

        // Give super_admin all permissions
        $superAdmin->syncPermissions(Permission::all());

        // 2. ADMIN - Manage all resources but not roles/permissions
        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web'
        ]);

        $adminPermissions = [
            // Analytics & Reports
            'view_analytics',
            'export_data',
            'generate_reports',

            // Management (but not role management)
            'manage_users',
            'manage_settings',
            'view_logs',

            // Notifications
            'send_notifications',
            'view_notifications',

            // All Smart City modules - CRUD
            'view_smart_farm',
            'create_smart_farm',
            'update_smart_farm',
            'delete_smart_farm',
            'view_smart_parking',
            'create_smart_parking',
            'update_smart_parking',
            'delete_smart_parking',
            'view_smart_traffic',
            'create_smart_traffic',
            'update_smart_traffic',
            'delete_smart_traffic',
            'view_smart_lighting',
            'create_smart_lighting',
            'update_smart_lighting',
            'delete_smart_lighting',
            'view_fire_alarm',
            'create_fire_alarm',
            'update_fire_alarm',
            'delete_fire_alarm',
            'view_smart_tank',
            'create_smart_tank',
            'update_smart_tank',
            'delete_smart_tank',
        ];

        $admin->syncPermissions($adminPermissions);

        // 3. MODERATOR - Can view and edit most resources, but not delete
        $moderator = Role::firstOrCreate([
            'name' => 'moderator',
            'guard_name' => 'web'
        ]);

        $moderatorPermissions = [
            // Limited analytics
            'view_analytics',
            'view_logs',

            // Notifications
            'view_notifications',

            // Smart City modules - View and Update only
            'view_smart_farm',
            'update_smart_farm',
            'view_smart_parking',
            'update_smart_parking',
            'view_smart_traffic',
            'update_smart_traffic',
            'view_smart_lighting',
            'update_smart_lighting',
            'view_fire_alarm',
            'update_fire_alarm',
            'view_smart_tank',
            'update_smart_tank',
        ];

        $moderator->syncPermissions($moderatorPermissions);

        // 4. USER - View-only access
        $user = Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'web'
        ]);

        $userPermissions = [
            // View-only access to modules
            'view_smart_farm',
            'view_smart_parking',
            'view_smart_traffic',
            'view_smart_lighting',
            'view_fire_alarm',
            'view_smart_tank',

            // View analytics
            'view_analytics',
        ];

        $user->syncPermissions($userPermissions);

        // 5. PANEL USER - Basic panel access (created by Shield by default)
        Role::firstOrCreate([
            'name' => 'panel_user',
            'guard_name' => 'web'
        ]);

        // ============================================================
        // CREATE TEST USERS
        // ============================================================

        // Super Admin User
        $superAdminUser = User::firstOrCreate(
            ['email' => 'admin@smartcity.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('SuperAdmin@123'),
            ]
        );
        $superAdminUser->assignRole('super_admin');

        // Admin User
        $adminUser = User::firstOrCreate(
            ['email' => 'admin.user@smartcity.test'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('Admin@123'),
            ]
        );
        $adminUser->assignRole('admin');

        // Moderator User
        $moderatorUser = User::firstOrCreate(
            ['email' => 'moderator@smartcity.test'],
            [
                'name' => 'Moderator User',
                'password' => Hash::make('Moderator@123'),
            ]
        );
        $moderatorUser->assignRole('moderator');

        // Regular User
        $regularUser = User::firstOrCreate(
            ['email' => 'user@smartcity.test'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('User@123'),
            ]
        );
        $regularUser->assignRole('user');

        $this->command->info('✅ Roles and Permissions created successfully!');
        $this->command->info('');
        $this->command->info('Test Users Created:');
        $this->command->info('-------------------');
        $this->command->info('Super Admin: admin@smartcity.test / SuperAdmin@123');
        $this->command->info('Admin: admin.user@smartcity.test / Admin@123');
        $this->command->info('Moderator: moderator@smartcity.test / Moderator@123');
        $this->command->info('User: user@smartcity.test / User@123');
    }
}
