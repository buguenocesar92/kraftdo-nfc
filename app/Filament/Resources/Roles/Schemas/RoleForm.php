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
                            ->relationship('permissions', 'name')
                            ->options(function () {
                                $permissions = Permission::all();
                                $grouped = [];
                                
                                foreach ($permissions as $permission) {
                                    $parts = explode('_', $permission->name);
                                    if (in_array($parts[0], ['view', 'create', 'update', 'delete'])) {
                                        $category = implode('_', array_slice($parts, 1));
                                    } else {
                                        $category = 'general';
                                    }
                                    
                                    $grouped[$category][$permission->name] = $permission->name;
                                }
                                
                                return $grouped;
                            })
                            ->descriptions([
                                'access_admin_panel' => 'Acceder al panel de administración',
                                'view_analytics' => 'Ver análisis y estadísticas',
                                'manage_system_settings' => 'Gestionar configuración del sistema',
                                'bulk_actions' => 'Realizar acciones en lote',
                                'view_dynamic_content' => 'Ver contenido dinámico propio',
                                'view_any_dynamic_content' => 'Ver todo el contenido dinámico',
                                'create_dynamic_content' => 'Crear contenido dinámico',
                                'update_dynamic_content' => 'Editar contenido dinámico',
                                'delete_dynamic_content' => 'Eliminar contenido dinámico propio',
                                'delete_any_dynamic_content' => 'Eliminar cualquier contenido dinámico',
                            ])
                            ->columns(3)
                            ->columnSpanFull()
                            ->searchable()
                            ->bulkToggleable(),
                    ]),
            ]);
    }
}
