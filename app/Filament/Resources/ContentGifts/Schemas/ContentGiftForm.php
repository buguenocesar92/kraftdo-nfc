<?php

namespace App\Filament\Resources\ContentGifts\Schemas;

use App\Models\DynamicContent;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContentGiftForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Regalo')
                    ->description('Configura los datos básicos del regalo')
                    ->schema([
                        Select::make('dynamic_content_id')
                            ->relationship(
                                name: 'dynamicContent', 
                                titleAttribute: 'title',
                                modifyQueryUsing: fn ($query) => $query->where('type', DynamicContent::TYPE_GIFT)
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->title} - {$record->content_id}")
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('sender_name')
                            ->label('Nombre del remitente')
                            ->required(),
                        TextInput::make('recipient_name')
                            ->label('Nombre del destinatario')
                            ->required(),
                        Textarea::make('message')
                            ->label('Mensaje personalizado')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Tema y Estilo')
                    ->description('Personaliza la apariencia del regalo')
                    ->schema([
                        Select::make('settings.theme')
                            ->label('Tema del Regalo')
                            ->options([
                                'love' => '💕 Amor - Colores rosas y rojos',
                                'birthday' => '🎂 Cumpleaños - Colores festivos y alegres',
                                'anniversary' => '💒 Aniversario - Colores dorados y elegantes',
                                'friendship' => '🤝 Amistad - Colores azules y verdes',
                                'graduation' => '🎓 Graduación - Colores académicos',
                                'christmas' => '🎄 Navidad - Colores navideños',
                                'valentine' => '💖 San Valentín - Rosa y rojo intenso',
                                'mother_day' => '🌸 Día de la Madre - Colores florales',
                                'father_day' => '👔 Día del Padre - Colores clásicos',
                                'congratulations' => '🎉 Felicitaciones - Colores vibrantes',
                            ])
                            ->default('love')
                            ->required()
                            ->columnSpan(2),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Contenido Multimedia')
                    ->description('Agrega video y audio al regalo')
                    ->schema([
                        // Video
                        Select::make('video_type')
                            ->label('Tipo de video')
                            ->options([
                                'direct' => 'URL externa',
                                'file_upload' => 'Archivo subido',
                                'youtube' => 'YouTube',
                                'vimeo' => 'Vimeo',
                            ])
                            ->default('direct')
                            ->columnSpan(1),
                        
                        TextInput::make('video_url')
                            ->label('URL del video')
                            ->placeholder('https://www.youtube.com/watch?v=...')
                            ->url()
                            ->columnSpan(1),
                        
                        FileUpload::make('video_file')
                            ->label('Archivo de video')
                            ->directory('videos')
                            ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                            ->maxSize(50 * 1024) // 50MB
                            ->disk('public')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->columnSpanFull(),

                        // Audio
                        Select::make('audio_type')
                            ->label('Tipo de audio')
                            ->options([
                                'direct' => 'URL externa',
                                'file_upload' => 'Archivo subido',
                                'youtube_music' => 'YouTube Music',
                                'spotify' => 'Spotify',
                                'soundcloud' => 'SoundCloud',
                            ])
                            ->default('direct')
                            ->columnSpan(1),
                        
                        TextInput::make('audio_url')
                            ->label('URL del audio')
                            ->placeholder('https://example.com/audio.mp3')
                            ->url()
                            ->columnSpan(1),
                        
                        FileUpload::make('audio_file')
                            ->label('Archivo de audio')
                            ->directory('audio')
                            ->acceptedFileTypes(['audio/mp3', 'audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/m4a', 'audio/x-m4a', 'audio/aac'])
                            ->maxSize(20 * 1024) // 20MB
                            ->disk('public')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Galería de Imágenes')
                    ->description('Agrega imágenes al regalo')
                    ->schema([
                        FileUpload::make('gallery_images')
                            ->label('Imágenes')
                            ->directory('gallery')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                            ->maxSize(10 * 1024) // 10MB por imagen
                            ->maxFiles(10)
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
