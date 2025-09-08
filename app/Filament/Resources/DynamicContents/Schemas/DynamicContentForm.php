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
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Group;
use Filament\Forms\Get;
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
                            ->default(fn () => \Illuminate\Support\Str::uuid()),
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
                        TextInput::make('multimedia.video_url')
                            ->label('URL del Video')
                            ->url(),
                        Select::make('multimedia.video_type')
                            ->label('Tipo de Video')
                            ->options([
                                'file_upload' => 'Archivo subido',
                                'youtube' => 'YouTube',
                                'vimeo' => 'Vimeo',
                                'direct' => 'URL directa'
                            ]),
                        TextInput::make('multimedia.audio_url')
                            ->label('URL del Audio')
                            ->url(),
                        Select::make('multimedia.audio_type')
                            ->label('Tipo de Audio')
                            ->options([
                                'file_upload' => 'Archivo subido',
                                'youtube_music' => 'YouTube Music',
                                'spotify' => 'Spotify',
                                'soundcloud' => 'SoundCloud',
                                'direct' => 'URL directa'
                            ]),
                        Repeater::make('multimedia.gallery_images')
                            ->label('Galería de Imágenes')
                            ->schema([
                                TextInput::make('url')
                                    ->label('URL de la imagen')
                                    ->url()
                                    ->required(),
                                TextInput::make('alt')
                                    ->label('Texto alternativo'),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Datos de Regalo')
                    ->schema([
                        TextInput::make('gift.sender_name')
                            ->label('Nombre del remitente'),
                        TextInput::make('gift.recipient_name')
                            ->label('Nombre del destinatario'),
                        Textarea::make('gift.message')
                            ->label('Mensaje')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Get $get) => $get('type') === DynamicContent::TYPE_GIFT)
                    ->columns(2)
                    ->collapsible(),

                Section::make('Datos del Menú')
                    ->schema([
                        TextInput::make('menu.restaurant_name')
                            ->label('Nombre del restaurante'),
                        TextInput::make('menu.restaurant_phone')
                            ->label('Teléfono del restaurante'),
                        Textarea::make('menu.restaurant_address')
                            ->label('Dirección del restaurante'),
                        TextInput::make('menu.restaurant_hours')
                            ->label('Horarios del restaurante'),
                        Repeater::make('menu.menu_items')
                            ->label('Items del menú')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre del plato')
                                    ->required(),
                                TextInput::make('price')
                                    ->label('Precio')
                                    ->numeric(),
                                Textarea::make('description')
                                    ->label('Descripción'),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Get $get) => $get('type') === DynamicContent::TYPE_MENU)
                    ->columns(2)
                    ->collapsible(),

                Section::make('Datos del Perfil')
                    ->schema([
                        TextInput::make('profile.contact_email')
                            ->label('Email de contacto')
                            ->email(),
                        TextInput::make('profile.contact_phone')
                            ->label('Teléfono de contacto'),
                        TextInput::make('profile.contact_website')
                            ->label('Sitio web')
                            ->url(),
                        Textarea::make('profile.bio')
                            ->label('Biografía')
                            ->columnSpanFull(),
                        Repeater::make('socialLinks')
                            ->label('Enlaces sociales')
                            ->relationship()
                            ->schema([
                                TextInput::make('platform')
                                    ->label('Plataforma')
                                    ->required(),
                                TextInput::make('url')
                                    ->label('URL')
                                    ->url()
                                    ->required(),
                                TextInput::make('username')
                                    ->label('Nombre de usuario'),
                            ])
                            ->columnSpanFull(),
                        Repeater::make('skills')
                            ->label('Habilidades')
                            ->relationship()
                            ->schema([
                                TextInput::make('name')
                                    ->label('Habilidad')
                                    ->required(),
                                TextInput::make('level')
                                    ->label('Nivel')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(10),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Get $get) => $get('type') === DynamicContent::TYPE_PROFILE)
                    ->columns(2)
                    ->collapsible(),

                Section::make('Datos del Evento')
                    ->schema([
                        TextInput::make('event.event_location')
                            ->label('Ubicación del evento'),
                        DateTimePicker::make('event.event_start_date')
                            ->label('Fecha y hora de inicio'),
                        DateTimePicker::make('event.event_end_date')
                            ->label('Fecha y hora de fin'),
                        TextInput::make('event.event_organizer')
                            ->label('Organizador del evento'),
                        Textarea::make('event.event_description')
                            ->label('Descripción del evento')
                            ->columnSpanFull(),
                        TextInput::make('event.event_capacity')
                            ->label('Capacidad')
                            ->numeric(),
                        Toggle::make('event.registration_required')
                            ->label('Requiere registro'),
                        TextInput::make('event.registration_url')
                            ->label('URL de registro')
                            ->url(),
                        TextInput::make('event.ticket_price')
                            ->label('Precio del boleto')
                            ->numeric()
                            ->prefix('$'),
                    ])
                    ->visible(fn (Get $get) => $get('type') === DynamicContent::TYPE_EVENT)
                    ->columns(2)
                    ->collapsible(),

                Section::make('Datos del Producto')
                    ->schema([
                        TextInput::make('product.product_price')
                            ->label('Precio')
                            ->numeric()
                            ->prefix('$'),
                        Select::make('product.product_currency')
                            ->label('Moneda')
                            ->options([
                                'USD' => 'Dólares (USD)',
                                'EUR' => 'Euros (EUR)',
                                'GBP' => 'Libras (GBP)',
                                'MXN' => 'Pesos mexicanos (MXN)',
                            ])
                            ->default('USD'),
                        TextInput::make('product.product_sku')
                            ->label('SKU'),
                        TextInput::make('product.product_stock')
                            ->label('Stock disponible')
                            ->numeric(),
                        Textarea::make('product.product_description')
                            ->label('Descripción del producto')
                            ->columnSpanFull(),
                        TextInput::make('product.product_weight')
                            ->label('Peso (kg)')
                            ->numeric(),
                        Select::make('product.availability_status')
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
                        TextInput::make('tourist.location_name')
                            ->label('Nombre del lugar'),
                        Textarea::make('tourist.location_address')
                            ->label('Dirección'),
                        TextInput::make('tourist.contact_phone')
                            ->label('Teléfono de contacto'),
                        TextInput::make('tourist.contact_email')
                            ->label('Email de contacto')
                            ->email(),
                        TextInput::make('tourist.website_url')
                            ->label('Sitio web')
                            ->url(),
                        Textarea::make('tourist.description')
                            ->label('Descripción')
                            ->columnSpanFull(),
                        TextInput::make('tourist.best_time_to_visit')
                            ->label('Mejor época para visitar'),
                        Repeater::make('tourist.attractions')
                            ->label('Atracciones principales')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre')
                                    ->required(),
                                Textarea::make('description')
                                    ->label('Descripción'),
                            ])
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
                        Select::make('user_id')
                            ->relationship('user', 'name'),
                        Select::make('nfc_token_id')
                            ->relationship('nfcToken', 'name'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
