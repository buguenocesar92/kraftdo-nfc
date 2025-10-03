<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles y permisos
        $this->call(RolesAndPermissionsSeeder::class);

        // Roles específicos NFC
        $this->call(NfcRoleSeeder::class);

        // Permisos para Content Business
        $this->call(ContentBusinessPermissionsSeeder::class);

        // Usuarios administradores y test
        $this->call(AdminUserSeeder::class);

        // Datos de demo NFC
        $this->call(NfcDemoSeeder::class);
    }
}
