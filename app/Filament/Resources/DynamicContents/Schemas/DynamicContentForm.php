<?php

namespace App\Filament\Resources\DynamicContents\Schemas;

use App\Models\DynamicContent;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;

class DynamicContentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información Básica')
                    ->schema([
                        TextInput::make('content_id')
                            ->required()
                            ->default(fn () => (string) \Illuminate\Support\Str::uuid()),
                        Select::make('type')
                            ->options(DynamicContent::TYPES)
                            ->required()
                            ->live(),
                        TextInput::make('gift_subtype')
                            ->visible(fn (Get $get) => $get('type') === DynamicContent::TYPE_GIFT),
                        TextInput::make('tier')
                            ->required()
                            ->default('sweet'),
                        TextInput::make('title')
                            ->required(),
                        Textarea::make('description')
                            ->columnSpanFull(),
                        FileUpload::make('image_url')
                            ->image(),
                    ])
                    ->columns(2),

                Section::make('Contenido Multimedia')
                    ->schema([
                        TextInput::make('video_url')
                            ->label('URL del Video')
                            ->url(),
                        Select::make('video_type')
                            ->label('Tipo de Video')
                            ->options([
                                'file_upload' => 'Archivo subido',
                                'youtube' => 'YouTube',
                                'vimeo' => 'Vimeo',
                                'direct' => 'URL directa'
                            ]),
                        TextInput::make('audio_url')
                            ->label('URL del Audio')
                            ->url(),
                        Select::make('audio_type')
                            ->label('Tipo de Audio')
                            ->options([
                                'file_upload' => 'Archivo subido',
                                'youtube_music' => 'YouTube Music',
                                'spotify' => 'Spotify',
                                'soundcloud' => 'SoundCloud',
                                'direct' => 'URL directa'
                            ]),
                        Textarea::make('gallery_images')
                            ->label('URLs de Galería (una por línea)')
                            ->rows(4)
                            ->helperText('Ingresa una URL por línea')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Get $get) => in_array($get('type'), [DynamicContent::TYPE_GIFT, DynamicContent::TYPE_MENU, DynamicContent::TYPE_PROFILE, DynamicContent::TYPE_EVENT, DynamicContent::TYPE_PRODUCT, DynamicContent::TYPE_TOURIST]))
                    ->columns(2)
                    ->collapsible(),

                Section::make('Datos de Regalo')
                    ->schema([
                        TextInput::make('sender_name')
                            ->label('Nombre del remitente'),
                        TextInput::make('recipient_name')
                            ->label('Nombre del destinatario'),
                        Textarea::make('gift_message')
                            ->label('Mensaje')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Get $get) => $get('type') === DynamicContent::TYPE_GIFT)
                    ->columns(2)
                    ->collapsible(),

                Section::make('Datos del Menú')
                    ->schema([
                        TextInput::make('restaurant_name')
                            ->label('Nombre del restaurante'),
                        TextInput::make('restaurant_phone')
                            ->label('Teléfono del restaurante'),
                        Textarea::make('restaurant_address')
                            ->label('Dirección del restaurante'),
                        TextInput::make('restaurant_hours')
                            ->label('Horarios del restaurante'),
                        Textarea::make('menu_items')
                            ->label('Items del menú (JSON)')
                            ->rows(8)
                            ->helperText('Formato JSON: [{"name": "Plato", "price": 15.99, "description": "Descripción"}]')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Get $get) => $get('type') === DynamicContent::TYPE_MENU)
                    ->columns(2)
                    ->collapsible(),

                Section::make('Datos del Perfil')
                    ->schema([
                        TextInput::make('contact_email')
                            ->label('Email de contacto')
                            ->email(),
                        TextInput::make('contact_phone')
                            ->label('Teléfono de contacto'),
                        TextInput::make('contact_website')
                            ->label('Sitio web')
                            ->url(),
                        Textarea::make('profile_bio')
                            ->label('Biografía')
                            ->columnSpanFull(),
                        Textarea::make('social_links_json')
                            ->label('Enlaces sociales (JSON)')
                            ->rows(6)
                            ->helperText('Formato JSON: [{"platform": "Instagram", "url": "https://...", "username": "@user"}]')
                            ->columnSpanFull(),
                        Textarea::make('skills_json')
                            ->label('Habilidades (JSON)')
                            ->rows(6)
                            ->helperText('Formato JSON: [{"name": "PHP", "level": 8}]')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Get $get) => $get('type') === DynamicContent::TYPE_PROFILE)
                    ->columns(2)
                    ->collapsible(),

                Section::make('Datos del Evento')
                    ->schema([
                        TextInput::make('event_location')
                            ->label('Ubicación del evento'),
                        DateTimePicker::make('event_start_date')
                            ->label('Fecha y hora de inicio'),
                        DateTimePicker::make('event_end_date')
                            ->label('Fecha y hora de fin'),
                        TextInput::make('event_organizer')
                            ->label('Organizador del evento'),
                        Textarea::make('event_description')
                            ->label('Descripción del evento')
                            ->columnSpanFull(),
                        TextInput::make('event_capacity')
                            ->label('Capacidad')
                            ->numeric(),
                        Toggle::make('registration_required')
                            ->label('Requiere registro'),
                        TextInput::make('registration_url')
                            ->label('URL de registro')
                            ->url(),
                        TextInput::make('ticket_price')
                            ->label('Precio del boleto')
                            ->numeric()
                            ->prefix('$'),
                    ])
                    ->visible(fn (Get $get) => $get('type') === DynamicContent::TYPE_EVENT)
                    ->columns(2)
                    ->collapsible(),

                Section::make('Datos del Producto')
                    ->schema([
                        TextInput::make('product_price')
                            ->label('Precio')
                            ->numeric()
                            ->prefix('$'),
                        Select::make('product_currency')
                            ->label('Moneda')
                            ->options([
                                'USD' => 'Dólares (USD)',
                                'EUR' => 'Euros (EUR)',
                                'GBP' => 'Libras (GBP)',
                                'MXN' => 'Pesos mexicanos (MXN)',
                            ])
                            ->default('USD'),
                        TextInput::make('product_sku')
                            ->label('SKU'),
                        TextInput::make('product_stock')
                            ->label('Stock disponible')
                            ->numeric(),
                        Textarea::make('product_description')
                            ->label('Descripción del producto')
                            ->columnSpanFull(),
                        TextInput::make('product_weight')
                            ->label('Peso (kg)')
                            ->numeric(),
                        Select::make('availability_status')
                            ->label('Estado de disponibilidad')
                            ->options([
                                'available' => 'Disponible',
                                'out_of_stock' => 'Agotado',
                                'discontinued' => 'Descontinuado',
                            ])
                            ->default('available'),
                    ])
                    ->visible(fn (Get $get) => $get('type') === DynamicContent::TYPE_PRODUCT)
                    ->columns(2)
                    ->collapsible(),

                Section::make('Información Turística')
                    ->schema([
                        TextInput::make('location_name')
                            ->label('Nombre del lugar'),
                        Textarea::make('location_address')
                            ->label('Dirección'),
                        TextInput::make('tourist_contact_phone')
                            ->label('Teléfono de contacto'),
                        TextInput::make('tourist_contact_email')
                            ->label('Email de contacto')
                            ->email(),
                        TextInput::make('website_url')
                            ->label('Sitio web')
                            ->url(),
                        Textarea::make('tourist_description')
                            ->label('Descripción')
                            ->columnSpanFull(),
                        TextInput::make('best_time_to_visit')
                            ->label('Mejor época para visitar'),
                        Textarea::make('attractions')
                            ->label('Atracciones principales (JSON)')
                            ->rows(6)
                            ->helperText('Formato JSON: [{"name": "Atracción", "description": "Descripción"}]')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Get $get) => $get('type') === DynamicContent::TYPE_TOURIST)
                    ->columns(2)
                    ->collapsible(),

                Section::make('Configuración JSON')
                    ->schema([
                        Textarea::make('data')
                            ->label('Configuraciones adicionales (JSON)')
                            ->columnSpanFull()
                            ->rows(8)
                            ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $state)
                            ->dehydrateStateUsing(function ($state) {
                                if (is_string($state)) {
                                    $decoded = json_decode($state, true);
                                    return json_last_error() === JSON_ERROR_NONE ? $decoded : $state;
                                }
                                return $state;
                            })
                            ->helperText('Formato JSON para configuraciones y datos de diseño.'),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Section::make('Estado y Metadatos')
                    ->schema([
                        Toggle::make('is_active')
                            ->required(),
                        Select::make('status')
                            ->options([
                                'draft' => 'Borrador',
                                'published' => 'Publicado',
                                'paused' => 'Pausado'
                            ])
                            ->required()
                            ->default('draft'),
                        DateTimePicker::make('published_at'),
                        TextInput::make('post_publish_modifications')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                        TextInput::make('user_id')
                            ->label('User ID')
                            ->numeric(),
                        TextInput::make('nfc_token_id')
                            ->label('NFC Token ID')
                            ->numeric(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
