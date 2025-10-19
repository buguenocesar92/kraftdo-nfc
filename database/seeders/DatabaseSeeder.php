<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles y permisos base del sistema
        $this->call(RolesAndPermissionsSeeder::class);

        // Roles específicos NFC
        $this->call(NfcRoleSeeder::class);

        // Permisos específicos para módulos
        $this->call(ContentBusinessPermissionsSeeder::class);
        $this->call(BusStopPermissionsSeeder::class);

        // Usuarios administradores del sistema
        $this->call(AdminUserSeeder::class);

        // Sistema listo para usar - sin datos de prueba
        // Para agregar datos de prueba, ejecuta seeders específicos manualmente
    }
}
