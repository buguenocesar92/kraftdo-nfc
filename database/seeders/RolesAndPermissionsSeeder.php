<?php

namespace Database\Seeders;

use App\Services\PermissionCacheService;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear todos los permisos de Filament Resources automáticamente
        $permissions = [
            // Content Menus
            'view_content_menus', 'view_any_content_menus', 'create_content_menus', 'update_content_menus', 'delete_content_menus', 'delete_any_content_menus',
            
            // Routes  
            'view_routes', 'view_any_routes', 'create_routes', 'update_routes', 'delete_routes', 'delete_any_routes',
            
            // Roles
            'view_roles', 'view_any_roles', 'create_roles', 'update_roles', 'delete_roles', 'delete_any_roles',
            
            // Users
            'view_users', 'view_any_users', 'create_users', 'update_users', 'delete_users', 'delete_any_users',
            
            // NFC Analytics
            'view_nfc_analytics', 'view_any_nfc_analytics', 'create_nfc_analytics', 'update_nfc_analytics', 'delete_nfc_analytics', 'delete_any_nfc_analytics',
            
            // NFC Tokens
            'view_nfc_tokens', 'view_any_nfc_tokens', 'create_nfc_tokens', 'update_nfc_tokens', 'delete_nfc_tokens', 'delete_any_nfc_tokens',
            
            // Content Gifts
            'view_content_gifts', 'view_any_content_gifts', 'create_content_gifts', 'update_content_gifts', 'delete_content_gifts', 'delete_any_content_gifts',
            
            // Content Business Groups
            'view_content_business_groups', 'view_any_content_business_groups', 'create_content_business_groups', 'update_content_business_groups', 'delete_content_business_groups', 'delete_any_content_business_groups',
            
            // Content Profiles
            'view_content_profiles', 'view_any_content_profiles', 'create_content_profiles', 'update_content_profiles', 'delete_content_profiles', 'delete_any_content_profiles',
            
            // Schedules
            'view_schedules', 'view_any_schedules', 'create_schedules', 'update_schedules', 'delete_schedules', 'delete_any_schedules',
            
            // Content Businesses
            'view_content_businesses', 'view_any_content_businesses', 'create_content_businesses', 'update_content_businesses', 'delete_content_businesses', 'delete_any_content_businesses',
            
            // Content Events
            'view_content_events', 'view_any_content_events', 'create_content_events', 'update_content_events', 'delete_content_events', 'delete_any_content_events',
            
            // Content Products
            'view_content_products', 'view_any_content_products', 'create_content_products', 'update_content_products', 'delete_content_products', 'delete_any_content_products',
            
            // Bus Stops
            'view_bus_stops', 'view_any_bus_stops', 'create_bus_stops', 'update_bus_stops', 'delete_bus_stops', 'delete_any_bus_stops',
            
            // Utility Phones
            'view_utility_phones', 'view_any_utility_phones', 'create_utility_phones', 'update_utility_phones', 'delete_utility_phones', 'delete_any_utility_phones',
            
            // Dynamic Contents
            'view_dynamic_contents', 'view_any_dynamic_contents', 'create_dynamic_contents', 'update_dynamic_contents', 'delete_dynamic_contents', 'delete_any_dynamic_contents',
            
            // Content Multimedia
            'view_content_multimedia', 'view_any_content_multimedia', 'create_content_multimedia', 'update_content_multimedia', 'delete_content_multimedia', 'delete_any_content_multimedia',
            
            // Content Tourists
            'view_content_tourists', 'view_any_content_tourists', 'create_content_tourists', 'update_content_tourists', 'delete_content_tourists', 'delete_any_content_tourists',

            // Permisos generales del sistema
            'access_admin_panel',
            'view_analytics',
            'manage_system_settings',
            'bulk_actions',

            // Permisos para gestión de tokens propios
            'view_own_tokens',
            'configure_own_tokens',
            'manage_own_token_content',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles con permisos

        // 1. Super Admin - Acceso completo a TODOS los permisos
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        // Sincronizar con TODOS los permisos existentes en el sistema
        $superAdmin->syncPermissions(Permission::all());

        // 2. Administrador - Puede gestionar contenido y usuarios
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->syncPermissions([
            'access_admin_panel',
            'view_analytics',

            // Gestión completa de contenido
            'view_any_dynamic_contents', 'create_dynamic_contents', 'update_dynamic_contents', 'delete_any_dynamic_contents',
            'view_any_content_gifts', 'create_content_gifts', 'update_content_gifts', 'delete_any_content_gifts',
            'view_any_content_profiles', 'create_content_profiles', 'update_content_profiles', 'delete_any_content_profiles',
            'view_any_content_menus', 'create_content_menus', 'update_content_menus', 'delete_any_content_menus',
            'view_any_content_events', 'create_content_events', 'update_content_events', 'delete_any_content_events',
            'view_any_content_products', 'create_content_products', 'update_content_products', 'delete_any_content_products',
            'view_any_content_tourists', 'create_content_tourists', 'update_content_tourists', 'delete_any_content_tourists',
            'view_any_content_businesses', 'create_content_businesses', 'update_content_businesses', 'delete_any_content_businesses',
            'view_any_content_business_groups', 'create_content_business_groups', 'update_content_business_groups', 'delete_any_content_business_groups',
            'view_any_content_multimedia', 'create_content_multimedia', 'update_content_multimedia', 'delete_any_content_multimedia',

            // Gestión de NFC Tokens
            'view_any_nfc_tokens', 'create_nfc_tokens', 'update_nfc_tokens', 'delete_any_nfc_tokens',

            // Gestión de usuarios limitada
            'view_any_users', 'create_users', 'update_users',

            // Transporte público
            'view_any_bus_stops', 'create_bus_stops', 'update_bus_stops', 'delete_any_bus_stops',
            'view_any_routes', 'create_routes', 'update_routes', 'delete_any_routes',
            'view_any_schedules', 'create_schedules', 'update_schedules', 'delete_any_schedules',
            'view_any_utility_phones', 'create_utility_phones', 'update_utility_phones', 'delete_any_utility_phones',

            'bulk_actions',
        ]);

        // 3. Editor - Puede crear y editar contenido
        $editor = Role::firstOrCreate(['name' => 'Editor']);
        $editor->syncPermissions([
            'access_admin_panel',

            // Gestión de contenido (contenido propio)
            'view_dynamic_contents', 'create_dynamic_contents', 'update_dynamic_contents',
            'view_content_gifts', 'create_content_gifts', 'update_content_gifts',
            'view_content_profiles', 'create_content_profiles', 'update_content_profiles',
            'view_content_menus', 'create_content_menus', 'update_content_menus',
            'view_content_events', 'create_content_events', 'update_content_events',
            'view_content_products', 'create_content_products', 'update_content_products',
            'view_content_tourists', 'create_content_tourists', 'update_content_tourists',
            'view_content_businesses', 'create_content_businesses', 'update_content_businesses',
            'view_content_multimedia', 'create_content_multimedia', 'update_content_multimedia',

            // Puede ver pero no gestionar tokens
            'view_nfc_tokens',

            // Gestión de tokens propios
            'view_own_tokens',
            'configure_own_tokens',
            'manage_own_token_content',
        ]);

        // 4. Visualizador - Acceso solo de lectura
        $viewer = Role::firstOrCreate(['name' => 'Viewer']);
        $viewer->syncPermissions([
            'access_admin_panel',

            // Permisos solo de visualización
            'view_dynamic_contents',
            'view_content_gifts',
            'view_content_profiles',
            'view_content_menus',
            'view_content_events',
            'view_content_products',
            'view_content_tourists',
            'view_content_businesses',
            'view_content_multimedia',
            'view_nfc_tokens',
            'view_bus_stops',
            'view_routes',
            'view_schedules',
            'view_utility_phones',
        ]);

        // 5. Gestor de Contenido - Especializado en tipos de contenido
        $contentManager = Role::firstOrCreate(['name' => 'Content Manager']);
        $contentManager->syncPermissions([
            'access_admin_panel',
            'view_analytics',

            // Gestión completa de contenido pero no gestión de usuarios/sistema
            'view_any_dynamic_contents', 'create_dynamic_contents', 'update_dynamic_contents', 'delete_dynamic_contents',
            'view_any_content_gifts', 'create_content_gifts', 'update_content_gifts', 'delete_content_gifts',
            'view_any_content_profiles', 'create_content_profiles', 'update_content_profiles', 'delete_content_profiles',
            'view_any_content_menus', 'create_content_menus', 'update_content_menus', 'delete_content_menus',
            'view_any_content_events', 'create_content_events', 'update_content_events', 'delete_content_events',
            'view_any_content_products', 'create_content_products', 'update_content_products', 'delete_content_products',
            'view_any_content_tourists', 'create_content_tourists', 'update_content_tourists', 'delete_content_tourists',
            'view_any_content_businesses', 'create_content_businesses', 'update_content_businesses', 'delete_content_businesses',
            'view_any_content_multimedia', 'create_content_multimedia', 'update_content_multimedia', 'delete_content_multimedia',

            // Gestión de NFC Tokens
            'view_any_nfc_tokens', 'create_nfc_tokens', 'update_nfc_tokens',

            'bulk_actions',
        ]);

        // 6. NFC User - Rol por defecto para usuarios registrados vía onboarding
        $nfcUser = Role::firstOrCreate(['name' => 'NFC']);
        $nfcUser->syncPermissions([
            'access_admin_panel',
            'view_own_tokens',
            'configure_own_tokens',
            'manage_own_token_content',
        ]);

        // Limpiar y refrescar cache de permisos después de crear/actualizar
        PermissionCacheService::refreshCache();
        
        $this->command->info('¡Roles y permisos creados exitosamente!');
    }
}
