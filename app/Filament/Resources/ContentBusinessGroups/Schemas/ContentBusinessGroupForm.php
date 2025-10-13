<?php

namespace App\Filament\Resources\ContentBusinessGroups\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Schema;

class ContentBusinessGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('dynamic_content_id')
                    ->relationship('dynamicContent', 'title')
                    ->searchable()
                    ->required()
                    ->label('Contenido Dinámico Asociado'),
                
                TextInput::make('group_name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nombre del Grupo'),
                
                Select::make('group_type')
                    ->options([
                        'food_court' => 'Food Court',
                        'mall' => 'Mall / Centro Comercial',
                        'market' => 'Mercado',
                        'fair' => 'Feria',
                        'plaza' => 'Plaza de Comidas',
                        'other' => 'Otro'
                    ])
                    ->required()
                    ->default('food_court')
                    ->label('Tipo de Grupo'),
                
                Textarea::make('description')
                    ->label('Descripción')
                    ->rows(3),
                
                TextInput::make('address')
                    ->label('Dirección'),
                
                TextInput::make('contact_phone')
                    ->tel()
                    ->label('Teléfono'),
                
                TextInput::make('contact_email')
                    ->email()
                    ->label('Email'),
                
                TextInput::make('contact_website')
                    ->url()
                    ->label('Sitio Web'),
                
                TextInput::make('logo_url')
                    ->url()
                    ->label('URL del Logo'),
                
                FileUpload::make('banner_image')
                    ->image()
                    ->label('Imagen de Banner'),
                
                Repeater::make('operating_hours')
                    ->label('Horarios de Operación')
                    ->schema([
                        Select::make('day')
                            ->label('Día')
                            ->options([
                                'monday' => 'Lunes',
                                'tuesday' => 'Martes',
                                'wednesday' => 'Miércoles',
                                'thursday' => 'Jueves',
                                'friday' => 'Viernes',
                                'saturday' => 'Sábado',
                                'sunday' => 'Domingo',
                            ])
                            ->required(),
                        TextInput::make('hours')
                            ->label('Horario')
                            ->placeholder('10:00-22:00')
                            ->required(),
                    ])
                    ->columns(2)
                    ->defaultItems(7)
                    ->default([
                        ['day' => 'monday', 'hours' => '10:00-22:00'],
                        ['day' => 'tuesday', 'hours' => '10:00-22:00'],
                        ['day' => 'wednesday', 'hours' => '10:00-22:00'],
                        ['day' => 'thursday', 'hours' => '10:00-22:00'],
                        ['day' => 'friday', 'hours' => '10:00-23:00'],
                        ['day' => 'saturday', 'hours' => '09:00-23:00'],
                        ['day' => 'sunday', 'hours' => '09:00-22:00'],
                    ])
                    ->formatStateUsing(function ($state) {
                        if (is_array($state) && !isset($state[0])) {
                            // Convert associative array to repeater format
                            $items = [];
                            foreach ($state as $day => $hours) {
                                $items[] = ['day' => $day, 'hours' => $hours];
                            }
                            return $items;
                        }
                        return $state;
                    })
                    ->dehydrateStateUsing(function ($state) {
                        if (is_array($state)) {
                            // Convert repeater format to associative array
                            $hours = [];
                            foreach ($state as $item) {
                                if (isset($item['day']) && isset($item['hours'])) {
                                    $hours[$item['day']] = $item['hours'];
                                }
                            }
                            return $hours;
                        }
                        return $state;
                    }),
                
                TextInput::make('latitude')
                    ->label('Latitud')
                    ->placeholder('-34.1853')
                    ->numeric()
                    ->step(0.000001)
                    ->formatStateUsing(function ($state, $record) {
                        return $record?->location_coordinates['lat'] ?? '';
                    })
                    ->dehydrated(false),
                
                TextInput::make('longitude')
                    ->label('Longitud')
                    ->placeholder('-70.6506')
                    ->numeric()
                    ->step(0.000001)
                    ->formatStateUsing(function ($state, $record) {
                        return $record?->location_coordinates['lng'] ?? '';
                    })
                    ->dehydrated(false),
                
                CheckboxList::make('amenities')
                    ->label('Comodidades Disponibles')
                    ->options([
                        'parking' => '🅿️ Estacionamiento',
                        'wifi' => '📶 WiFi Gratuito',
                        'restrooms' => '🚻 Baños',
                        'playground' => '🎮 Área de Juegos',
                        'eco_trails' => '🌿 Senderos Ecológicos',
                        'live_music' => '🎵 Música en Vivo',
                        'family_area' => '👨‍👩‍👧‍👦 Área Familiar',
                        'pet_friendly' => '🐕 Pet Friendly',
                        'delivery' => '🚚 Delivery',
                        'outdoor_seating' => '🪑 Mesas al Aire Libre',
                        'air_conditioning' => '❄️ Aire Acondicionado',
                        'security' => '🔒 Seguridad 24/7',
                        'handicap_accessible' => '♿ Acceso para Discapacitados',
                        'valet_parking' => '🚗 Valet Parking',
                        'food_court' => '🍽️ Patio de Comidas',
                        'atm' => '🏧 Cajero Automático',
                    ])
                    ->columns(3),
                
                Textarea::make('special_instructions')
                    ->label('Instrucciones Especiales')
                    ->placeholder('Horarios especiales, eventos, promociones, etc.')
                    ->rows(3),
                
                Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true),
            ]);
    }
}
