<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SyncSuperAdminPermissions extends Command
{
    protected $signature = 'shield:sync-super-admin';
    protected $description = 'Sync all permissions to super_admin role';

    public function handle()
    {
        $superAdmin = Role::where('name', 'super_admin')->first();

        if (!$superAdmin) {
            $this->error('Super admin role not found!');
            return 1;
        }

        // Give super admin ALL permissions
        $allPermissions = Permission::all();
        $superAdmin->syncPermissions($allPermissions);

        $this->info("✅ Super admin now has {$allPermissions->count()} permissions");

        // List all permissions
        $this->newLine();
        $this->info('All available permissions:');
        foreach ($allPermissions as $permission) {
            $this->line("  - {$permission->name}");
        }

        return 0;
    }
}
