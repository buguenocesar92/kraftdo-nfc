<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class NfcRoleSeeder extends Seeder
{
    /**
     * Create the NFC role for onboarding users
     */
    public function run(): void
    {
        // Verificar si el rol NFC ya existe
        $nfcRole = Role::where('name', 'NFC')->first();

        if ($nfcRole) {
            $this->command->info('Rol NFC ya existe, actualizando permisos...');
        } else {
            $this->command->info('Creando rol NFC...');
            $nfcRole = Role::create(['name' => 'NFC']);
        }

        // Permisos limitados para usuarios NFC - solo acceso al panel y sus propios tokens
        $nfcPermissions = [
            'access_admin_panel', // Permiso básico para acceder al panel admin
            'view_own_tokens',    // Solo puede ver sus propios tokens
            'configure_own_tokens', // Solo puede configurar sus propios tokens
            'manage_own_token_content', // Solo puede gestionar contenido de sus tokens
        ];

        // Obtener solo los permisos que existen
        $existingPermissions = Permission::whereIn('name', $nfcPermissions)->get();

        $this->command->info('Permisos encontrados: ' . $existingPermissions->count());

        // Asignar permisos al rol NFC
        $nfcRole->syncPermissions($existingPermissions);

        $this->command->info('Rol NFC configurado con ' . $nfcRole->permissions()->count() . ' permisos');
        $this->command->info('✅ Rol NFC listo para usuarios de onboarding');
    }
}
