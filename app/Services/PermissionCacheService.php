<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;

class PermissionCacheService
{
    const CACHE_TTL = 3600; // 1 hora
    const COLLECTION_KEY = 'all_permissions_collection';
    const MAP_KEY = 'all_permissions_map';
    const OPTIONS_KEY = 'permissions_options';
    const DESCRIPTIONS_KEY = 'permissions_descriptions';

    // Cache en memoria para evitar múltiples deserializaciones
    private static $memoryCache = [
        'permissions' => null,
        'map' => null,
        'options' => null,
        'descriptions' => null,
    ];

    /**
     * Obtener todos los permisos como ARRAY (no objetos Eloquent)
     */
    public static function getAllPermissions()
    {
        if (self::$memoryCache['permissions'] === null) {
            self::$memoryCache['permissions'] = Cache::remember(self::COLLECTION_KEY, self::CACHE_TTL, function () {
                // Usar DB facade directamente para evitar objetos Eloquent
                return \DB::table('permissions')->select(['id', 'name'])->get()->toArray();
            });
        }
        return self::$memoryCache['permissions'];
    }

    /**
     * Obtener mapa de permisos por ID como ARRAY
     */
    public static function getPermissionsMap()
    {
        if (self::$memoryCache['map'] === null) {
            self::$memoryCache['map'] = Cache::remember(self::MAP_KEY, self::CACHE_TTL, function () {
                // Crear array asociativo directamente sin objetos
                $permissions = \DB::table('permissions')->select(['id', 'name'])->get();
                $map = [];
                foreach ($permissions as $permission) {
                    $map[$permission->id] = (object) ['id' => $permission->id, 'name' => $permission->name];
                }
                return $map;
            });
        }
        return self::$memoryCache['map'];
    }

    /**
     * Obtener opciones para CheckboxList (id => name) - PURO ARRAY
     */
    public static function getPermissionsOptions()
    {
        if (self::$memoryCache['options'] === null) {
            self::$memoryCache['options'] = Cache::remember(self::OPTIONS_KEY, self::CACHE_TTL, function () {
                // Pluck directo sin objetos Eloquent
                return Permission::select(['id', 'name'])->pluck('name', 'id')->toArray();
            });
        }
        return self::$memoryCache['options'];
    }

    /**
     * Obtener descripciones pre-calculadas como PURO ARRAY
     */
    public static function getPermissionsDescriptions()
    {
        if (self::$memoryCache['descriptions'] === null) {
            self::$memoryCache['descriptions'] = Cache::remember(self::DESCRIPTIONS_KEY, self::CACHE_TTL, function () {
                $descriptions = [];
                
                // Usar DB directo para evitar objetos Eloquent
                $permissions = \DB::table('permissions')->select(['id', 'name'])->get();
                
                foreach ($permissions as $permission) {
                    $descriptions[$permission->id] = self::getPermissionDescription($permission->name);
                }
                
                return $descriptions;
            });
        }
        return self::$memoryCache['descriptions'];
    }

    /**
     * Limpiar toda la cache
     */
    public static function clearCache(): void
    {
        Cache::forget(self::COLLECTION_KEY);
        Cache::forget(self::MAP_KEY);
        Cache::forget(self::OPTIONS_KEY);
        Cache::forget(self::DESCRIPTIONS_KEY);
        
        // Limpiar cache en memoria
        self::$memoryCache = [
            'permissions' => null,
            'map' => null,
            'options' => null,
            'descriptions' => null,
        ];
    }

    /**
     * Refrescar cache de permisos
     */
    public static function refreshCache(): void
    {
        self::clearCache();
        self::getAllPermissions();
        self::getPermissionsMap();
        self::getPermissionsOptions();
        self::getPermissionsDescriptions();
    }

    /**
     * Obtener descripción de un permiso específico
     */
    private static function getPermissionDescription(string $name): string
    {
        return match ($name) {
            // Sistema general
            'access_admin_panel' => '🔑 Acceder al panel de administración',
            'view_analytics' => '📊 Ver análisis y estadísticas del sistema',
            'manage_system_settings' => '⚙️ Gestionar configuración del sistema',
            'bulk_actions' => '📦 Realizar acciones masivas en registros',
            
            // Tokens propios
            'view_own_tokens' => '🔖 Ver sus propios tokens NFC',
            'configure_own_tokens' => '⚙️ Configurar sus propios tokens NFC',
            'manage_own_token_content' => '📝 Gestionar contenido de sus tokens',

            // Dynamic Contents
            'view_dynamic_contents' => '👁️ Ver contenidos dinámicos',
            'view_any_dynamic_contents' => '🌐 Ver todos los contenidos dinámicos',
            'create_dynamic_contents' => '➕ Crear contenidos dinámicos',
            'update_dynamic_contents' => '✏️ Editar contenidos dinámicos',
            'delete_dynamic_contents' => '🗑️ Eliminar contenidos dinámicos',
            'delete_any_dynamic_contents' => '❌ Eliminar cualquier contenido dinámico',

            // Content Gifts
            'view_content_gifts' => '🎁 Ver regalos',
            'view_any_content_gifts' => '🎁 Ver todos los regalos',
            'create_content_gifts' => '➕ Crear regalos',
            'update_content_gifts' => '✏️ Editar regalos',
            'delete_content_gifts' => '🗑️ Eliminar regalos',
            'delete_any_content_gifts' => '❌ Eliminar cualquier regalo',

            // Content Profiles
            'view_content_profiles' => '👤 Ver perfiles',
            'view_any_content_profiles' => '👥 Ver todos los perfiles',
            'create_content_profiles' => '➕ Crear perfiles',
            'update_content_profiles' => '✏️ Editar perfiles',
            'delete_content_profiles' => '🗑️ Eliminar perfiles',
            'delete_any_content_profiles' => '❌ Eliminar cualquier perfil',

            // Content Menus
            'view_content_menus' => '🍽️ Ver menús',
            'view_any_content_menus' => '🍽️ Ver todos los menús',
            'create_content_menus' => '➕ Crear menús',
            'update_content_menus' => '✏️ Editar menús',
            'delete_content_menus' => '🗑️ Eliminar menús',
            'delete_any_content_menus' => '❌ Eliminar cualquier menú',

            // Content Events
            'view_content_events' => '📅 Ver eventos',
            'view_any_content_events' => '📅 Ver todos los eventos',
            'create_content_events' => '➕ Crear eventos',
            'update_content_events' => '✏️ Editar eventos',
            'delete_content_events' => '🗑️ Eliminar eventos',
            'delete_any_content_events' => '❌ Eliminar cualquier evento',

            // Content Products
            'view_content_products' => '🛍️ Ver productos',
            'view_any_content_products' => '🛍️ Ver todos los productos',
            'create_content_products' => '➕ Crear productos',
            'update_content_products' => '✏️ Editar productos',
            'delete_content_products' => '🗑️ Eliminar productos',
            'delete_any_content_products' => '❌ Eliminar cualquier producto',

            // Content Businesses
            'view_content_businesses' => '🏢 Ver negocios',
            'view_any_content_businesses' => '🏢 Ver todos los negocios',
            'create_content_businesses' => '➕ Crear negocios',
            'update_content_businesses' => '✏️ Editar negocios',
            'delete_content_businesses' => '🗑️ Eliminar negocios',
            'delete_any_content_businesses' => '❌ Eliminar cualquier negocio',

            // Content Business Groups
            'view_content_business_groups' => '🏪 Ver grupos de negocios',
            'view_any_content_business_groups' => '🏪 Ver todos los grupos de negocios',
            'create_content_business_groups' => '➕ Crear grupos de negocios',
            'update_content_business_groups' => '✏️ Editar grupos de negocios',
            'delete_content_business_groups' => '🗑️ Eliminar grupos de negocios',
            'delete_any_content_business_groups' => '❌ Eliminar cualquier grupo de negocios',

            // Content Tourists
            'view_content_tourists' => '🧳 Ver contenido turístico',
            'view_any_content_tourists' => '🧳 Ver todo el contenido turístico',
            'create_content_tourists' => '➕ Crear contenido turístico',
            'update_content_tourists' => '✏️ Editar contenido turístico',
            'delete_content_tourists' => '🗑️ Eliminar contenido turístico',
            'delete_any_content_tourists' => '❌ Eliminar cualquier contenido turístico',

            // Content Multimedia
            'view_content_multimedia' => '🎬 Ver multimedia',
            'view_any_content_multimedia' => '🎬 Ver toda la multimedia',
            'create_content_multimedia' => '➕ Crear multimedia',
            'update_content_multimedia' => '✏️ Editar multimedia',
            'delete_content_multimedia' => '🗑️ Eliminar multimedia',
            'delete_any_content_multimedia' => '❌ Eliminar cualquier multimedia',

            // NFC Tokens
            'view_nfc_tokens' => '🔖 Ver tokens NFC',
            'view_any_nfc_tokens' => '🔖 Ver todos los tokens NFC',
            'create_nfc_tokens' => '➕ Crear tokens NFC',
            'update_nfc_tokens' => '✏️ Editar tokens NFC',
            'delete_nfc_tokens' => '🗑️ Eliminar tokens NFC',
            'delete_any_nfc_tokens' => '❌ Eliminar cualquier token NFC',

            // NFC Analytics
            'view_nfc_analytics' => '📈 Ver analíticas NFC',
            'view_any_nfc_analytics' => '📈 Ver todas las analíticas NFC',
            'create_nfc_analytics' => '➕ Crear analíticas NFC',
            'update_nfc_analytics' => '✏️ Editar analíticas NFC',
            'delete_nfc_analytics' => '🗑️ Eliminar analíticas NFC',
            'delete_any_nfc_analytics' => '❌ Eliminar cualquier analítica NFC',

            // Users
            'view_users' => '👤 Ver usuarios',
            'view_any_users' => '👥 Ver todos los usuarios',
            'create_users' => '➕ Crear usuarios',
            'update_users' => '✏️ Editar usuarios',
            'delete_users' => '🗑️ Eliminar usuarios',
            'delete_any_users' => '❌ Eliminar cualquier usuario',

            // Roles
            'view_roles' => '🛡️ Ver roles',
            'view_any_roles' => '🛡️ Ver todos los roles',
            'create_roles' => '➕ Crear roles',
            'update_roles' => '✏️ Editar roles',
            'delete_roles' => '🗑️ Eliminar roles',
            'delete_any_roles' => '❌ Eliminar cualquier rol',

            // Bus Stops (Paraderos)
            'view_bus_stops' => '🚌 Ver paraderos',
            'view_any_bus_stops' => '🚌 Ver todos los paraderos',
            'create_bus_stops' => '➕ Crear paraderos',
            'update_bus_stops' => '✏️ Editar paraderos',
            'delete_bus_stops' => '🗑️ Eliminar paraderos',
            'delete_any_bus_stops' => '❌ Eliminar cualquier paradero',

            // Routes
            'view_routes' => '🛤️ Ver rutas',
            'view_any_routes' => '🛤️ Ver todas las rutas',
            'create_routes' => '➕ Crear rutas',
            'update_routes' => '✏️ Editar rutas',
            'delete_routes' => '🗑️ Eliminar rutas',
            'delete_any_routes' => '❌ Eliminar cualquier ruta',

            // Schedules
            'view_schedules' => '🕐 Ver horarios',
            'view_any_schedules' => '🕐 Ver todos los horarios',
            'create_schedules' => '➕ Crear horarios',
            'update_schedules' => '✏️ Editar horarios',
            'delete_schedules' => '🗑️ Eliminar horarios',
            'delete_any_schedules' => '❌ Eliminar cualquier horario',

            // Utility Phones
            'view_utility_phones' => '📞 Ver teléfonos útiles',
            'view_any_utility_phones' => '📞 Ver todos los teléfonos útiles',
            'create_utility_phones' => '➕ Crear teléfonos útiles',
            'update_utility_phones' => '✏️ Editar teléfonos útiles',
            'delete_utility_phones' => '🗑️ Eliminar teléfonos útiles',
            'delete_any_utility_phones' => '❌ Eliminar cualquier teléfono útil',

            // Default para otros permisos
            default => '📋 ' . ucfirst(str_replace('_', ' ', $name))
        };
    }
}