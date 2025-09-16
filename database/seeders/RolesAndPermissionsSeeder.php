<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for NFC Content Management
        $permissions = [
            // DynamicContent permissions
            'view_dynamic_content',
            'view_any_dynamic_content',
            'create_dynamic_content',
            'update_dynamic_content',
            'delete_dynamic_content',
            'delete_any_dynamic_content',

            // ContentGift permissions
            'view_content_gift',
            'view_any_content_gift',
            'create_content_gift',
            'update_content_gift',
            'delete_content_gift',
            'delete_any_content_gift',

            // ContentProfile permissions
            'view_content_profile',
            'view_any_content_profile',
            'create_content_profile',
            'update_content_profile',
            'delete_content_profile',
            'delete_any_content_profile',

            // ContentMenu permissions
            'view_content_menu',
            'view_any_content_menu',
            'create_content_menu',
            'update_content_menu',
            'delete_content_menu',
            'delete_any_content_menu',

            // ContentEvent permissions
            'view_content_event',
            'view_any_content_event',
            'create_content_event',
            'update_content_event',
            'delete_content_event',
            'delete_any_content_event',

            // ContentProduct permissions
            'view_content_product',
            'view_any_content_product',
            'create_content_product',
            'update_content_product',
            'delete_content_product',
            'delete_any_content_product',

            // ContentTourist permissions
            'view_content_tourist',
            'view_any_content_tourist',
            'create_content_tourist',
            'update_content_tourist',
            'delete_content_tourist',
            'delete_any_content_tourist',

            // NfcToken permissions
            'view_nfc_token',
            'view_any_nfc_token',
            'create_nfc_token',
            'update_nfc_token',
            'delete_nfc_token',
            'delete_any_nfc_token',

            // User management permissions
            'view_user',
            'view_any_user',
            'create_user',
            'update_user',
            'delete_user',
            'delete_any_user',

            // Role management permissions
            'view_role',
            'view_any_role',
            'create_role',
            'update_role',
            'delete_role',
            'delete_any_role',

            // General admin permissions
            'access_admin_panel',
            'view_analytics',
            'manage_system_settings',
            'bulk_actions',

            // Token management permissions for users
            'view_own_tokens',
            'configure_own_tokens',
            'manage_own_token_content',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles with permissions

        // 1. Super Admin - Full access
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->syncPermissions(Permission::all());

        // 2. Admin - Can manage content and users
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->syncPermissions([
            'access_admin_panel',
            'view_analytics',
            
            // Full content management
            'view_any_dynamic_content', 'create_dynamic_content', 'update_dynamic_content', 'delete_any_dynamic_content',
            'view_any_content_gift', 'create_content_gift', 'update_content_gift', 'delete_any_content_gift',
            'view_any_content_profile', 'create_content_profile', 'update_content_profile', 'delete_any_content_profile',
            'view_any_content_menu', 'create_content_menu', 'update_content_menu', 'delete_any_content_menu',
            'view_any_content_event', 'create_content_event', 'update_content_event', 'delete_any_content_event',
            'view_any_content_product', 'create_content_product', 'update_content_product', 'delete_any_content_product',
            'view_any_content_tourist', 'create_content_tourist', 'update_content_tourist', 'delete_any_content_tourist',
            
            // NFC Token management
            'view_any_nfc_token', 'create_nfc_token', 'update_nfc_token', 'delete_any_nfc_token',
            
            // Limited user management
            'view_any_user', 'create_user', 'update_user',
            
            'bulk_actions',
        ]);

        // 3. Editor - Can create and edit content
        $editor = Role::firstOrCreate(['name' => 'Editor']);
        $editor->syncPermissions([
            'access_admin_panel',
            
            // Content management (own content)
            'view_dynamic_content', 'create_dynamic_content', 'update_dynamic_content',
            'view_content_gift', 'create_content_gift', 'update_content_gift',
            'view_content_profile', 'create_content_profile', 'update_content_profile',
            'view_content_menu', 'create_content_menu', 'update_content_menu',
            'view_content_event', 'create_content_event', 'update_content_event',
            'view_content_product', 'create_content_product', 'update_content_product',
            'view_content_tourist', 'create_content_tourist', 'update_content_tourist',
            
            // Can view but not manage tokens
            'view_nfc_token',
            
            // Own tokens management
            'view_own_tokens',
            'configure_own_tokens',
            'manage_own_token_content',
        ]);

        // 4. Viewer - Read-only access
        $viewer = Role::firstOrCreate(['name' => 'Viewer']);
        $viewer->syncPermissions([
            'access_admin_panel',
            
            // View-only permissions
            'view_dynamic_content',
            'view_content_gift',
            'view_content_profile',
            'view_content_menu',
            'view_content_event',
            'view_content_product',
            'view_content_tourist',
            'view_nfc_token',
        ]);

        // 5. Content Manager - Specialized for content types
        $contentManager = Role::firstOrCreate(['name' => 'Content Manager']);
        $contentManager->syncPermissions([
            'access_admin_panel',
            'view_analytics',
            
            // Full content management but no user/system management
            'view_any_dynamic_content', 'create_dynamic_content', 'update_dynamic_content', 'delete_dynamic_content',
            'view_any_content_gift', 'create_content_gift', 'update_content_gift', 'delete_content_gift',
            'view_any_content_profile', 'create_content_profile', 'update_content_profile', 'delete_content_profile',
            'view_any_content_menu', 'create_content_menu', 'update_content_menu', 'delete_content_menu',
            'view_any_content_event', 'create_content_event', 'update_content_event', 'delete_content_event',
            'view_any_content_product', 'create_content_product', 'update_content_product', 'delete_content_product',
            'view_any_content_tourist', 'create_content_tourist', 'update_content_tourist', 'delete_content_tourist',
            
            // NFC Token management
            'view_any_nfc_token', 'create_nfc_token', 'update_nfc_token',
            
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

        $this->command->info('Roles and permissions created successfully!');
    }
}