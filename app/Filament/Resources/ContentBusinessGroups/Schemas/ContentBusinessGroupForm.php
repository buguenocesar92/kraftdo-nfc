<?php

namespace App\Filament\Resources\ContentBusinessGroups\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ContentBusinessGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Campo oculto - se crea automáticamente
                // Select::make('dynamic_content_id')
                //     ->relationship('dynamicContent', 'title')
                //     ->label('Contenido Dinámico Asociado')
                //     ->disabled()
                //     ->helperText('Se crea automáticamente con los datos del grupo'),

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
                        'other' => 'Otro',
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

                TextInput::make('google_place_id')
                    ->label('Google Place ID')
                    ->placeholder('ChIJN165LjbZYpYRpVEG8L8m1k8')
                    ->helperText('ID del lugar en Google Maps. Puedes obtenerlo desde Google Place ID Finder.')
                    ->maxLength(255),

                TextInput::make('google_reviews_url')
                    ->url()
                    ->label('URL de Reseñas de Google')
                    ->placeholder('https://g.page/r/CBNeW_qBgKGlEAE/review')
                    ->helperText('URL directa a las reseñas del lugar en Google.')
                    ->maxLength(255),

                FileUpload::make('logo_url')
                    ->image()
                    ->label('Logo del Grupo')
                    ->disk('public')
                    ->directory('business-groups/logos')
                    ->visibility('public')
                    ->maxSize(2048)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('1:1')
                    ->imageResizeTargetWidth('400')
                    ->imageResizeTargetHeight('400'),

                FileUpload::make('banner_image')
                    ->image()
                    ->label('Imagen de Banner')
                    ->disk('public')
                    ->directory('business-groups/banners')
                    ->visibility('public')
                    ->maxSize(5120)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('16:9')
                    ->imageResizeTargetWidth('1200')
                    ->imageResizeTargetHeight('675'),

                Repeater::make('operating_hours')
                    ->label('Horarios de Operación')
                    ->live()
                    ->afterStateUpdated(function ($state) {
                        \Log::info('🕐 BusinessGroup - afterStateUpdated:', ['state' => $state]);
                    })
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
                            ->helperText('Formato: HH:MM-HH:MM o "Cerrado"')
                            ->required(),
                    ])
                    ->columns(2)
                    ->defaultItems(1)
                    ->default([
                        ['day' => 'monday', 'hours' => '10:00-22:00'],
                    ])
                    ->addActionLabel('Agregar Día')
                    ->reorderable(false)
                    ->collapsible()
                    ->helperText('Agrega los horarios de operación para cada día. Puedes agregar tantos días como necesites.')
                    ->formatStateUsing(function ($state) {
                        // Si el estado es null o vacío, usar el valor por defecto
                        if (empty($state)) {
                            return [['day' => 'monday', 'hours' => '10:00-22:00']];
                        }

                        // Si es un array asociativo (formato de BD), convertir a formato repeater
                        if (is_array($state) && ! isset($state[0])) {
                            $items = [];
                            foreach ($state as $day => $hours) {
                                $items[] = ['day' => $day, 'hours' => $hours];
                            }

                            return $items;
                        }

                        // Si ya está en formato repeater, devolverlo tal como está
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
