<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Usuario')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre Completo')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->helperText('Deja en blanco para mantener la contraseña actual al editar'),

                        DateTimePicker::make('email_verified_at')
                            ->label('Email Verificado')
                            ->displayFormat('d/m/Y H:i')
                            ->helperText('Fecha y hora cuando el email fue verificado'),
                    ])
                    ->columns(2),

                Section::make('Roles y Permisos')
                    ->schema([
                        CheckboxList::make('roles')
                            ->label('Asignar Roles')
                            ->options(function () {
                                return Role::all()->pluck('name', 'id');
                            })
                            ->descriptions(function () {
                                $descriptions = [];
                                foreach (Role::all() as $role) {
                                    $descriptions[$role->id] = match($role->name) {
                                        'Super Admin' => '🔴 Acceso completo al sistema - ¡Usar con precaución!',
                                        'Admin' => '🟠 Gestión completa de contenido y usuarios limitada',
                                        'Editor' => '🟡 Crear y editar su propio contenido',
                                        'Viewer' => '🟢 Solo lectura de contenido',
                                        'Content Manager' => '🔵 Gestión especializada de contenido sin acceso a usuarios',
                                        default => '📋 Rol personalizado'
                                    };
                                }
                                return $descriptions;
                            })
                            ->columns(2)
                            ->columnSpanFull()
                            ->helperText('Los roles determinan qué acciones puede realizar el usuario en el sistema'),
                    ]),

                Section::make('Información del Sistema')
                    ->schema([
                        TextInput::make('created_at')
                            ->label('Creado')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn ($record) => $record),

                        TextInput::make('updated_at')
                            ->label('Actualizado')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn ($record) => $record),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }
}
