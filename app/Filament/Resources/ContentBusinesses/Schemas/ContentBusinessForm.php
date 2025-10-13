<?php

namespace App\Filament\Resources\ContentBusinesses\Schemas;

use App\Models\DynamicContent;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\TagsInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContentBusinessForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información Básica del Negocio')
                    ->description('Configura la información principal del negocio o feria')
                    ->schema([
                        Select::make('dynamic_content_id')
                            ->relationship(
                                name: 'dynamicContent', 
                                titleAttribute: 'title',
                                modifyQueryUsing: fn ($query) => $query->where('type', DynamicContent::TYPE_BUSINESS)
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->title} - {$record->content_id}")
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                        
                        Select::make('business_type')
                            ->label('Tipo de Negocio')
                            ->options([
                                'restaurant' => '🍽️ Restaurante / Café',
                                'retail' => '🛍️ Tienda / Retail',
                                'service' => '🔧 Servicios',
                                'fair' => '🎪 Feria / Evento',
                                'other' => '📋 Otro',
                            ])
                            ->default('other')
                            ->required()
                            ->columnSpan(1),

                        TextInput::make('business_name')
                            ->label('Nombre del Negocio')
                            ->placeholder('Ej: Café Central, Feria de Artesanías...')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),
                        
                        Textarea::make('description')
                            ->label('Descripción')
                            ->placeholder('Describe tu negocio, productos o servicios...')
                            ->rows(4)
                            ->columnSpanFull(),
                        
                        Select::make('business_type')
                            ->label('Tipo de Negocio')
                            ->options([
                                'restaurante' => '🍽️ Restaurante',
                                'cafe' => '☕ Café',
                                'tienda' => '🛍️ Tienda',
                                'feria' => '🎪 Feria',
                                'artesania' => '🎨 Artesanía',
                                'servicios' => '🔧 Servicios',
                                'salon_belleza' => '💅 Salón de Belleza',
                                'gym' => '💪 Gimnasio',
                                'consultorio' => '🏥 Consultorio',
                                'farmacia' => '💊 Farmacia',
                                'otro' => '🏢 Otro',
                            ])
                            ->searchable()
                            ->required(),
                        
                        FileUpload::make('logo_url')
                            ->label('Logo del Negocio')
                            ->directory('businesses/logos')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                            ->maxSize(5 * 1024) // 5MB
                            ->disk('public')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->imageEditor()
                            ->imagePreviewHeight('200')
                            ->helperText('Logo que se mostrará en la tarjeta del negocio'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Información de Contacto')
                    ->description('Datos de contacto y ubicación')
                    ->schema([
                        TextInput::make('contact_phone')
                            ->label('Teléfono')
                            ->placeholder('+56 9 1234 5678')
                            ->tel(),
                        
                        TextInput::make('contact_email')
                            ->label('Email')
                            ->email()
                            ->placeholder('contacto@negocio.com'),
                        
                        TextInput::make('contact_website')
                            ->label('Sitio Web')
                            ->url()
                            ->placeholder('https://mi-negocio.com')
                            ->columnSpanFull(),
                        
                        Textarea::make('address')
                            ->label('Dirección')
                            ->placeholder('Calle 123, Ciudad, Región')
                            ->rows(2)
                            ->columnSpanFull(),
                        
                        TextInput::make('whatsapp_number')
                            ->label('WhatsApp')
                            ->placeholder('+56912345678')
                            ->helperText('Número de WhatsApp (se generará el enlace automáticamente)'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Redes Sociales')
                    ->description('Enlaces a redes sociales del negocio')
                    ->schema([
                        TextInput::make('instagram_url')
                            ->label('Instagram')
                            ->url()
                            ->placeholder('https://instagram.com/mi_negocio')
                            ->prefixIcon('heroicon-o-camera'),
                        
                        TextInput::make('facebook_url')
                            ->label('Facebook')
                            ->url()
                            ->placeholder('https://facebook.com/mi-negocio')
                            ->prefixIcon('heroicon-o-heart'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Google Integration')
                    ->description('Configuración de Google Maps y Reviews')
                    ->schema([
                        TextInput::make('google_maps_url')
                            ->label('URL de Google Maps')
                            ->url()
                            ->placeholder('https://maps.google.com/maps?q=...')
                            ->helperText('URL directa de Google Maps para tu ubicación'),
                        
                        TextInput::make('google_reviews_url')
                            ->label('URL de Google Reviews')
                            ->url()
                            ->placeholder('https://g.page/r/...')
                            ->helperText('URL para que los clientes dejen reseñas'),
                        
                        TextInput::make('google_place_id')
                            ->label('Google Place ID')
                            ->placeholder('ChIJ...')
                            ->helperText('ID del lugar en Google Places (opcional, para integración avanzada)'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Horarios de Atención')
                    ->description('Define los horarios del negocio')
                    ->schema([
                        Repeater::make('operating_hours')
                            ->label('Horarios')
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
                                    ->placeholder('09:00-18:00 o Cerrado')
                                    ->helperText('Formato: 09:00-18:00 o escribir "Cerrado"'),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->maxItems(7)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Servicios y Catálogo')
                    ->description('Configura los servicios y catálogo de productos')
                    ->schema([
                        TagsInput::make('services')
                            ->label('Servicios')
                            ->placeholder('Delivery, Estacionamiento, WiFi, etc.')
                            ->helperText('Presiona Enter para agregar cada servicio')
                            ->columnSpanFull(),
                        
                        Toggle::make('catalog_enabled')
                            ->label('Habilitar Catálogo de Productos')
                            ->helperText('Permite mostrar productos en la tarjeta del negocio')
                            ->live()
                            ->columnSpanFull(),
                        
                        Repeater::make('directProducts')
                            ->relationship('directProducts')
                            ->label('Productos del Catálogo')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre del Producto')
                                    ->placeholder('Ej: Hamburguesa Classic, Café Americano...')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                
                                TextInput::make('sku')
                                    ->label('Código/SKU')
                                    ->placeholder('PROD-001')
                                    ->maxLength(255),
                                
                                TextInput::make('price')
                                    ->label('Precio')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required(),
                                
                                Select::make('currency')
                                    ->label('Moneda')
                                    ->options([
                                        'CLP' => 'CLP (Peso Chileno)',
                                        'USD' => 'USD (Dólar)',
                                        'EUR' => 'EUR (Euro)',
                                    ])
                                    ->default('CLP')
                                    ->required(),
                                
                                TextInput::make('brand')
                                    ->label('Marca')
                                    ->placeholder('Marca del producto')
                                    ->maxLength(255),
                                
                                TextInput::make('stock')
                                    ->label('Stock')
                                    ->numeric()
                                    ->placeholder('Cantidad disponible'),
                                
                                Toggle::make('in_stock')
                                    ->label('En Stock')
                                    ->default(true),
                                
                                Textarea::make('specifications')
                                    ->label('Especificaciones/Descripción')
                                    ->placeholder('Descripción del producto, ingredientes, características...')
                                    ->rows(3)
                                    ->columnSpanFull(),
                                
                                TextInput::make('purchase_url')
                                    ->label('URL de Compra')
                                    ->url()
                                    ->placeholder('https://tienda.com/producto')
                                    ->helperText('Enlace directo para comprar el producto')
                                    ->columnSpanFull(),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->addActionLabel('Agregar Producto')
                            ->visible(fn ($get) => $get('catalog_enabled'))
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Personalización')
                    ->description('Personaliza los colores de la tarjeta')
                    ->schema([
                        ColorPicker::make('color_palette.primary')
                            ->label('Color Primario')
                            ->default('#3B82F6')
                            ->helperText('Color principal de la tarjeta'),
                            
                        ColorPicker::make('color_palette.secondary')
                            ->label('Color Secundario')
                            ->default('#8B5CF6')
                            ->helperText('Color secundario para elementos destacados'),
                            
                        ColorPicker::make('color_palette.accent')
                            ->label('Color de Acento')
                            ->default('#EC4899')
                            ->helperText('Color para botones y enlaces'),
                    ])
                    ->columns(3)
                    ->collapsible(),
            ]);
    }
}