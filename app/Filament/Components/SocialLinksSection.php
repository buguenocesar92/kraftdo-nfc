<?php

namespace App\Filament\Components;

use App\Models\ContentSocialLink;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

class SocialLinksSection
{
    public static function make(): Section
    {
        return Section::make('Enlaces de Redes Sociales')
            ->schema([
                Repeater::make('socialLinks')
                    ->label('Redes Sociales')
                    ->schema([
                        Select::make('platform')
                            ->label('Plataforma')
                            ->options(array_map(
                                fn($platform) => $platform['name'],
                                ContentSocialLink::PLATFORMS
                            ))
                            ->required()
                            ->live(),
                        TextInput::make('url')
                            ->label('URL Completa')
                            ->url()
                            ->required()
                            ->helperText('Incluye https://'),
                        TextInput::make('username')
                            ->label('Nombre de usuario (opcional)')
                            ->helperText('Solo el nombre de usuario, sin @'),
                        TextInput::make('sort_order')
                            ->label('Orden')
                            ->numeric()
                            ->default(0)
                            ->helperText('Número para ordenar (0 = primero)'),
                    ])
                    ->columns(2)
                    ->defaultItems(0)
                    ->addActionLabel('Agregar red social')
                    ->reorderableWithButtons()
                    ->collapsible()
                    ->columnSpanFull(),
            ])
            ->collapsible();
    }
}