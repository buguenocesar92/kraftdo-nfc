<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ContentBusinessPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Crear permisos para ContentBusiness
        $permissions = [
            'view_any_content_business',
            'view_content_business',
            'create_content_business',
            'update_content_business',
            'delete_content_business',
            'delete_any_content_business',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Asignar permisos a roles existentes
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permissions);
        }

        $nfcRole = Role::where('name', 'NFC')->first();
        if ($nfcRole) {
            // Solo permisos básicos para usuarios NFC
            $nfcRole->givePermissionTo([
                'view_any_content_business',
                'view_content_business',
                'create_content_business',
                'update_content_business',
            ]);
        }

        $this->command->info('Content Business permissions created and assigned successfully!');
    }
}
