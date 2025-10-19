<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Permission;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Rol')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre del Rol')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        TextInput::make('guard_name')
                            ->label('Guard')
                            ->default('web')
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(2),

                Section::make('Permisos')
                    ->schema([
                        CheckboxList::make('permissions')
                            ->label('Seleccionar Permisos')
                            ->options(function () {
                                return Permission::all()->pluck('name', 'id');
                            })
                            ->descriptions(function () {
                                $descriptions = [];
                                foreach (Permission::all() as $permission) {
                                    $name = $permission->name;

                                    // Descripciones específicas para cada permiso
                                    $descriptions[$permission->id] = match ($name) {
                                        // Sistema general
                                        'access_admin_panel' => '🔑 Acceder al panel de administración',
                                        'view_analytics' => '📊 Ver análisis y estadísticas del sistema',
                                        'manage_system_settings' => '⚙️ Gestionar configuración del sistema',
                                        'bulk_actions' => '📦 Realizar acciones masivas en registros',

                                        // Dynamic Content
                                        'view_dynamic_content' => '👁️ Ver su propio contenido dinámico',
                                        'view_any_dynamic_content' => '🌐 Ver todo el contenido dinámico del sistema',
                                        'create_dynamic_content' => '➕ Crear nuevo contenido dinámico',
                                        'update_dynamic_content' => '✏️ Editar contenido dinámico',
                                        'delete_dynamic_content' => '🗑️ Eliminar su propio contenido dinámico',
                                        'delete_any_dynamic_content' => '❌ Eliminar cualquier contenido dinámico',

                                        // Content Gift
                                        'view_content_gift' => '🎁 Ver sus propios regalos',
                                        'view_any_content_gift' => '🎁 Ver todos los regalos del sistema',
                                        'create_content_gift' => '➕ Crear nuevos regalos',
                                        'update_content_gift' => '✏️ Editar regalos',
                                        'delete_content_gift' => '🗑️ Eliminar sus propios regalos',
                                        'delete_any_content_gift' => '❌ Eliminar cualquier regalo',

                                        // Content Profile
                                        'view_content_profile' => '👤 Ver sus propios perfiles',
                                        'view_any_content_profile' => '👥 Ver todos los perfiles del sistema',
                                        'create_content_profile' => '➕ Crear nuevos perfiles',
                                        'update_content_profile' => '✏️ Editar perfiles',
                                        'delete_content_profile' => '🗑️ Eliminar sus propios perfiles',
                                        'delete_any_content_profile' => '❌ Eliminar cualquier perfil',

                                        // Content Menu
                                        'view_content_menu' => '🍽️ Ver sus propios menús',
                                        'view_any_content_menu' => '🍽️ Ver todos los menús del sistema',
                                        'create_content_menu' => '➕ Crear nuevos menús',
                                        'update_content_menu' => '✏️ Editar menús',
                                        'delete_content_menu' => '🗑️ Eliminar sus propios menús',
                                        'delete_any_content_menu' => '❌ Eliminar cualquier menú',

                                        // NFC Token
                                        'view_nfc_token' => '🔖 Ver tokens NFC',
                                        'view_any_nfc_token' => '🔖 Ver todos los tokens NFC',
                                        'create_nfc_token' => '➕ Crear nuevos tokens NFC',
                                        'update_nfc_token' => '✏️ Editar tokens NFC',
                                        'delete_nfc_token' => '🗑️ Eliminar tokens NFC',
                                        'delete_any_nfc_token' => '❌ Eliminar cualquier token NFC',

                                        // Usuarios
                                        'view_user' => '👤 Ver usuarios',
                                        'view_any_user' => '👥 Ver todos los usuarios',
                                        'create_user' => '➕ Crear nuevos usuarios',
                                        'update_user' => '✏️ Editar usuarios',
                                        'delete_user' => '🗑️ Eliminar usuarios',
                                        'delete_any_user' => '❌ Eliminar cualquier usuario',

                                        // Roles
                                        'view_role' => '🛡️ Ver roles',
                                        'view_any_role' => '🛡️ Ver todos los roles',
                                        'create_role' => '➕ Crear nuevos roles',
                                        'update_role' => '✏️ Editar roles',
                                        'delete_role' => '🗑️ Eliminar roles',
                                        'delete_any_role' => '❌ Eliminar cualquier rol',

                                        // Default para otros permisos
                                        default => '📋 ' . ucfirst(str_replace('_', ' ', $name))
                                    };
                                }

                                return $descriptions;
                            })
                            ->columns(2)
                            ->columnSpanFull()
                            ->searchable()
                            ->bulkToggleable()
                            ->gridDirection('row')
                            ->helperText('Selecciona los permisos que tendrá este rol. Los permisos "any" permiten acciones sobre todos los registros, mientras que los normales solo sobre los propios.'),
                    ]),
            ]);
    }
}
