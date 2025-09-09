<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

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

        // Definir permisos para usuarios NFC - solo acceso al panel
        $nfcPermissions = [
            'access_admin_panel'
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