<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Services\PermissionCacheService;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

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
                                // Usar cache pre-calculado de opciones (evita pluck() en runtime)
                                return PermissionCacheService::getPermissionsOptions();
                            })
                            ->descriptions(function () {
                                // Usar cache pre-calculado de descripciones
                                return PermissionCacheService::getPermissionsDescriptions();
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
