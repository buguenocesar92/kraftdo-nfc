<?php

namespace App\Filament\Resources\ContentProfiles\Schemas;

use App\Models\DynamicContent;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContentProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Perfil')
                    ->description('Configura la información básica del perfil')
                    ->schema([
                        Select::make('dynamic_content_id')
                            ->relationship(
                                name: 'dynamicContent',
                                titleAttribute: 'title',
                                modifyQueryUsing: function ($query, $livewire) {
                                    $query->where('type', DynamicContent::TYPE_PROFILE);

                                    // Excluir DynamicContent ya asignados a otros ContentProfile
                                    $assignedIds = \App\Models\ContentProfile::pluck('dynamic_content_id')->filter();

                                    // En modo edición, permitir el contenido actualmente asignado
                                    if ($livewire->record) {
                                        $assignedIds = $assignedIds->reject(fn ($id) => $id === $livewire->record->dynamic_content_id);
                                    }

                                    if ($assignedIds->isNotEmpty()) {
                                        $query->whereNotIn('id', $assignedIds);
                                    }

                                    return $query;
                                }
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->title} - {$record->content_id}")
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Selecciona contenido dinámico disponible...')
                            ->helperText('Solo se muestran contenidos tipo PROFILE que no estén asignados a otros perfiles')
                            ->columnSpanFull(),

                        TextInput::make('name')
                            ->label('Nombre completo')
                            ->placeholder('Nombre completo del perfil')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('bio')
                            ->label('Biografía')
                            ->placeholder('Información sobre la persona o empresa...')
                            ->rows(4)
                            ->columnSpanFull(),

                        TextInput::make('profession')
                            ->label('Profesión/Cargo')
                            ->placeholder('Ej: Desarrollador Senior, CEO, Diseñador...')
                            ->maxLength(255),

                        TextInput::make('company')
                            ->label('Empresa/Organización')
                            ->placeholder('Nombre de la empresa o organización')
                            ->maxLength(255),

                        TextInput::make('location')
                            ->label('Ubicación')
                            ->placeholder('Ciudad, País')
                            ->maxLength(255),

                        TextInput::make('contact_info')
                            ->label('Información de Contacto Principal')
                            ->placeholder('Email principal o método de contacto preferido')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('contact_email')
                            ->label('Correo Electrónico Adicional')
                            ->email()
                            ->placeholder('contacto@ejemplo.com'),

                        TextInput::make('contact_phone')
                            ->label('Teléfono')
                            ->placeholder('+1 234 567 8900'),

                        TextInput::make('contact_website')
                            ->label('Sitio Web')
                            ->url()
                            ->placeholder('https://ejemplo.com')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Paleta de Colores')
                    ->description('Personaliza los colores del perfil')
                    ->schema([
                        ColorPicker::make('color_palette.primary')
                            ->label('Color Primario')
                            ->default('#3B82F6')
                            ->helperText('Color principal del gradiente de fondo'),

                        ColorPicker::make('color_palette.secondary')
                            ->label('Color Secundario')
                            ->default('#8B5CF6')
                            ->helperText('Color secundario del gradiente'),

                        ColorPicker::make('color_palette.accent')
                            ->label('Color Terciario')
                            ->default('#EC4899')
                            ->helperText('Color terciario del gradiente'),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Section::make('Enlaces Sociales')
                    ->description('Agrega redes sociales y enlaces importantes')
                    ->schema([
                        Repeater::make('social_links')
                            ->label('Enlaces Sociales')
                            ->schema([
                                Select::make('platform')
                                    ->label('Plataforma')
                                    ->options([
                                        'instagram' => '📷 Instagram',
                                        'linkedin' => '💼 LinkedIn',
                                        'twitter' => '🐦 Twitter/X',
                                        'facebook' => '📘 Facebook',
                                        'tiktok' => '🎵 TikTok',
                                        'youtube' => '📹 YouTube',
                                        'github' => '💻 GitHub',
                                        'whatsapp' => '💬 WhatsApp',
                                        'website' => '🌐 Sitio Web',
                                    ])
                                    ->required()
                                    ->columnSpan(1),

                                TextInput::make('username')
                                    ->label('Usuario/Nombre')
                                    ->placeholder('nombre_usuario')
                                    ->columnSpan(1),

                                TextInput::make('url')
                                    ->label('URL Completa (opcional)')
                                    ->url()
                                    ->placeholder('https://...')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->reorderable()
                            ->defaultItems(0)
                            ->maxItems(8)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Contenido Multimedia')
                    ->description('Agrega contenido visual al perfil')
                    ->schema([
                        // Imagen de perfil principal
                        FileUpload::make('settings.profile_image')
                            ->label('Imagen de Perfil')
                            ->directory('profiles')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                            ->maxSize(5 * 1024) // 5MB
                            ->disk('public')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->imageEditor()
                            ->imagePreviewHeight('200')
                            ->helperText('Imagen principal del perfil. Se mostrará en forma circular.')
                            ->columnSpan(1),

                        // Video de presentación
                        Select::make('video_type')
                            ->label('Tipo de video de presentación')
                            ->options([
                                'direct' => 'URL externa',
                                'file_upload' => 'Archivo subido',
                                'youtube' => 'YouTube',
                                'vimeo' => 'Vimeo',
                            ])
                            ->default('direct')
                            ->columnSpan(1),

                        TextInput::make('video_url')
                            ->label('URL del video de presentación')
                            ->placeholder('https://www.youtube.com/watch?v=...')
                            ->url()
                            ->columnSpanFull(),

                        FileUpload::make('video_file')
                            ->label('Archivo de video de presentación')
                            ->directory('profiles/videos')
                            ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                            ->maxSize(50 * 1024) // 50MB
                            ->disk('public')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->columnSpanFull(),

                        // Documentos PDF (CV, portafolio, catálogo, etc.)
                        FileUpload::make('settings.pdf_files')
                            ->label('Documentos PDF')
                            ->directory('profiles/documents')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(10 * 1024) // 10MB por archivo
                            ->maxFiles(5)
                            ->multiple()
                            ->reorderable()
                            ->disk('public')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->helperText('Sube documentos PDF (CV, portafolio, catálogo…). Máximo 10MB por archivo.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Galería de Imágenes')
                    ->description('Agrega imágenes adicionales al perfil')
                    ->schema([
                        FileUpload::make('gallery_images')
                            ->label('Galería de Imágenes')
                            ->directory('profiles/gallery')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                            ->maxSize(10 * 1024) // 10MB por imagen
                            ->maxFiles(12)
                            ->multiple()
                            ->reorderable()
                            ->disk('public')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->imageEditor()
                            ->imagePreviewHeight('150')
                            ->panelLayout('grid')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }
}
