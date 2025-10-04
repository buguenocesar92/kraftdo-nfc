<?php

namespace App\Filament\Resources\ContentTourists\Schemas;

use App\Models\ContentTourist;
use App\Models\DynamicContent;
use App\Models\NearbySpot;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\KeyValue;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContentTouristForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Información Básica')
                    ->schema([
                        Select::make('dynamic_content_id')
                            ->relationship(
                                name: 'dynamicContent', 
                                titleAttribute: 'title',
                                modifyQueryUsing: fn ($query) => $query->where('type', DynamicContent::TYPE_TOURIST)
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->title} - {$record->content_id}")
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                        
                        Grid::make(2)
                            ->schema([
                                TextInput::make('location_name')
                                    ->label('Nombre del Lugar')
                                    ->required()
                                    ->maxLength(255),
                                
                                Select::make('place_type')
                                    ->label('Tipo de Lugar')
                                    ->options(ContentTourist::getPlaceTypes())
                                    ->required(),
                            ]),
                        
                        
                        Textarea::make('location_address')
                            ->label('Dirección')
                            ->columnSpanFull()
                            ->rows(2),
                    ])
                    ->columnSpan(2),

                Section::make('Ubicación')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('latitude')
                                    ->label('Latitud')
                                    ->numeric()
                                    ->step(0.00000001)
                                    ->placeholder('-34.123456'),
                                
                                TextInput::make('longitude')
                                    ->label('Longitud')
                                    ->numeric()
                                    ->step(0.00000001)
                                    ->placeholder('-70.123456'),
                            ]),
                    ])
                    ->columnSpan(1),

                Section::make('Contacto')
                    ->schema([
                        TextInput::make('contact_phone')
                            ->label('Teléfono')
                            ->tel(),
                        
                        TextInput::make('contact_email')
                            ->label('Email')
                            ->email(),
                        
                        TextInput::make('website_url')
                            ->label('Sitio Web')
                            ->url(),
                    ])
                    ->columnSpan(1),

                Section::make('Historia y Descripción')
                    ->schema([
                        RichEditor::make('history')
                            ->label('Historia del Lugar')
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                                'link',
                            ]),
                    ])
                    ->columnSpan(2),

                Section::make('Galería de Imágenes')
                    ->schema([
                        FileUpload::make('gallery_images')
                            ->label('Imágenes')
                            ->multiple()
                            ->image()
                            ->reorderable()
                            ->disk('public')
                            ->directory('tourist-gallery')
                            ->maxFiles(10)
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(2),

                Section::make('Información Práctica')
                    ->schema([
                        KeyValue::make('opening_hours')
                            ->label('Horarios de Apertura')
                            ->keyLabel('Día')
                            ->valueLabel('Horario')
                            ->default([
                                'monday' => '09:00 - 18:00',
                                'tuesday' => '09:00 - 18:00',
                                'wednesday' => '09:00 - 18:00',
                                'thursday' => '09:00 - 18:00',
                                'friday' => '09:00 - 18:00',
                                'saturday' => '10:00 - 16:00',
                                'sunday' => 'Cerrado',
                            ])
                            ->columnSpanFull(),

                        KeyValue::make('pricing_info')
                            ->label('Información de Precios')
                            ->keyLabel('Concepto')
                            ->valueLabel('Precio')
                            ->default([
                                'adulto' => 'Gratis',
                                'niño' => 'Gratis',
                                'estudiante' => 'Gratis',
                            ])
                            ->columnSpanFull(),

                        KeyValue::make('accessibility_info')
                            ->label('Información de Accesibilidad')
                            ->keyLabel('Aspecto')
                            ->valueLabel('Descripción')
                            ->default([
                                'wheelchair' => 'Accesible',
                                'parking' => 'Disponible',
                                'restrooms' => 'Disponibles',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(2),

                Section::make('Servicios y Atracciones')
                    ->schema([
                        KeyValue::make('services')
                            ->label('Servicios Disponibles')
                            ->keyLabel('Servicio')
                            ->valueLabel('Descripción')
                            ->default([
                                'guias' => 'Disponibles',
                                'cafeteria' => 'No disponible',
                                'tienda' => 'No disponible',
                            ])
                            ->columnSpanFull(),

                        KeyValue::make('attractions')
                            ->label('Atracciones Principales')
                            ->keyLabel('Atracción')
                            ->valueLabel('Descripción')
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('best_time_to_visit')
                                    ->label('Mejor Época para Visitar')
                                    ->placeholder('Todo el año'),

                                KeyValue::make('languages_spoken')
                                    ->label('Idiomas')
                                    ->keyLabel('Idioma')
                                    ->valueLabel('Nivel')
                                    ->default([
                                        'español' => 'Nativo',
                                        'inglés' => 'Básico',
                                    ]),
                            ]),
                    ])
                    ->columnSpan(2),

                Section::make('Lugares Cercanos')
                    ->schema([
                        Repeater::make('nearbySpots')
                            ->relationship()
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nombre')
                                            ->required(),

                                        Select::make('spot_type')
                                            ->label('Tipo')
                                            ->options(array_map(fn($type) => $type['label'], NearbySpot::getSpotTypes()))
                                            ->required(),

                                        TextInput::make('distance_km')
                                            ->label('Distancia (km)')
                                            ->numeric()
                                            ->step(0.01),
                                    ]),

                                Textarea::make('description')
                                    ->label('Descripción')
                                    ->rows(2),

                                Grid::make(4)
                                    ->schema([
                                        TextInput::make('latitude')
                                            ->label('Latitud')
                                            ->numeric()
                                            ->step(0.00000001)
                                            ->required(),

                                        TextInput::make('longitude')
                                            ->label('Longitud')
                                            ->numeric()
                                            ->step(0.00000001)
                                            ->required(),

                                        TextInput::make('color')
                                            ->label('Color')
                                            ->placeholder('#3B82F6'),

                                        TextInput::make('icon')
                                            ->label('Icono')
                                            ->placeholder('map-pin'),
                                    ]),

                                KeyValue::make('additional_info')
                                    ->label('Información Adicional')
                                    ->keyLabel('Campo')
                                    ->valueLabel('Valor'),
                            ])
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                            ->addActionLabel('Agregar Lugar Cercano')
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(2),
            ]);
    }
}
