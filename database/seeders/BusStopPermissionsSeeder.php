<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class BusStopPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // BusStop permissions
            'view_any_bus_stop',
            'view_bus_stop',
            'create_bus_stop',
            'update_bus_stop',
            'delete_bus_stop',
            'delete_any_bus_stop',
            
            // Route permissions
            'view_any_route',
            'view_route',
            'create_route',
            'update_route',
            'delete_route',
            'delete_any_route',
            
            // Schedule permissions
            'view_any_schedule',
            'view_schedule',
            'create_schedule',
            'update_schedule',
            'delete_schedule',
            'delete_any_schedule',
            
            // UtilityPhone permissions
            'view_any_utility_phone',
            'view_utility_phone',
            'create_utility_phone',
            'update_utility_phone',
            'delete_utility_phone',
            'delete_any_utility_phone',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $superAdmin = Role::where('name', 'Super Admin')->first();
        $admin = Role::where('name', 'Admin')->first();
        $contentManager = Role::where('name', 'Content Manager')->first();
        $editor = Role::where('name', 'Editor')->first();
        $viewer = Role::where('name', 'Viewer')->first();

        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }

        if ($admin) {
            $admin->givePermissionTo([
                'view_any_bus_stop', 'create_bus_stop', 'update_bus_stop', 'delete_any_bus_stop',
                'view_any_route', 'create_route', 'update_route', 'delete_any_route',
                'view_any_schedule', 'create_schedule', 'update_schedule', 'delete_any_schedule',
                'view_any_utility_phone', 'create_utility_phone', 'update_utility_phone', 'delete_any_utility_phone',
            ]);
        }

        if ($contentManager) {
            $contentManager->givePermissionTo([
                'view_any_bus_stop', 'create_bus_stop', 'update_bus_stop', 'delete_bus_stop',
                'view_any_route', 'create_route', 'update_route', 'delete_route',
                'view_any_schedule', 'create_schedule', 'update_schedule', 'delete_schedule',
                'view_any_utility_phone', 'create_utility_phone', 'update_utility_phone', 'delete_utility_phone',
            ]);
        }

        if ($editor) {
            $editor->givePermissionTo([
                'view_bus_stop', 'create_bus_stop', 'update_bus_stop',
                'view_route', 'create_route', 'update_route',
                'view_schedule', 'create_schedule', 'update_schedule',
                'view_utility_phone', 'create_utility_phone', 'update_utility_phone',
            ]);
        }

        if ($viewer) {
            $viewer->givePermissionTo([
                'view_bus_stop',
                'view_route',
                'view_schedule',
                'view_utility_phone',
            ]);
        }
    }
}