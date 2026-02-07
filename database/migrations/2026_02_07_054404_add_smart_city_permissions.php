<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            'view_smart_parking',
            'view_smart_farm',
            'view_smart_tank',
            'view_smart_parking_revenue',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'view_smart_parking',
            'view_smart_farm',
            'view_smart_tank',
            'view_smart_parking_revenue',
        ];

        Permission::whereIn('name', $permissions)->delete();
    }
};
