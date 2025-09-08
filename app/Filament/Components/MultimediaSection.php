<?php

namespace App\Filament\Components;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;

class MultimediaSection
{
    public static function make(): Section
    {
        return Section::make('Contenido Multimedia')
            ->schema([
                Group::make([
                    Select::make('dynamicContent.multimedia.video_type')
                        ->label('Tipo de Video')
                        ->options([
                            'file_upload' => 'Archivo subido',
                            'youtube' => 'YouTube',
                            'vimeo' => 'Vimeo',
                            'direct' => 'URL directa'
                        ])
                        ->live()
                        ->afterStateUpdated(fn ($state, callable $set) => 
                            $state !== 'file_upload' ? $set('dynamicContent.multimedia.video_file', null) : null
                        ),
                    
                    FileUpload::make('dynamicContent.multimedia.video_file')
                        ->label('Subir Video')
                        ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg', 'video/avi', 'video/mov'])
                        ->maxSize(100 * 1024) // 100MB
                        ->directory('multimedia/videos')
                        ->visibility('public')
                        ->visible(fn (callable $get) => $get('dynamicContent.multimedia.video_type') === 'file_upload')
                        ->helperText('Formatos: MP4, WebM, OGG, AVI, MOV. Máximo 100MB'),
                    
                    TextInput::make('dynamicContent.multimedia.video_url')
                        ->label('URL del Video')
                        ->url()
                        ->visible(fn (callable $get) => in_array($get('dynamicContent.multimedia.video_type'), ['youtube', 'vimeo', 'direct']))
                        ->helperText('Para YouTube, Vimeo o enlaces directos'),
                ])
                ->columns(1)
                ->columnSpan(1),

                Group::make([
                    Select::make('dynamicContent.multimedia.audio_type')
                        ->label('Tipo de Audio')
                        ->options([
                            'file_upload' => 'Archivo subido',
                            'youtube_music' => 'YouTube Music',
                            'spotify' => 'Spotify',
                            'soundcloud' => 'SoundCloud',
                            'direct' => 'URL directa'
                        ])
                        ->live()
                        ->afterStateUpdated(fn ($state, callable $set) => 
                            $state !== 'file_upload' ? $set('dynamicContent.multimedia.audio_file', null) : null
                        ),
                    
                    FileUpload::make('dynamicContent.multimedia.audio_file')
                        ->label('Subir Audio')
                        ->acceptedFileTypes(['audio/mp3', 'audio/wav', 'audio/ogg', 'audio/aac', 'audio/flac'])
                        ->maxSize(50 * 1024) // 50MB
                        ->directory('multimedia/audio')
                        ->visibility('public')
                        ->visible(fn (callable $get) => $get('dynamicContent.multimedia.audio_type') === 'file_upload')
                        ->helperText('Formatos: MP3, WAV, OGG, AAC, FLAC. Máximo 50MB'),
                    
                    TextInput::make('dynamicContent.multimedia.audio_url')
                        ->label('URL del Audio')
                        ->url()
                        ->visible(fn (callable $get) => in_array($get('dynamicContent.multimedia.audio_type'), ['youtube_music', 'spotify', 'soundcloud', 'direct']))
                        ->helperText('Para plataformas de música o enlaces directos'),
                ])
                ->columns(1)
                ->columnSpan(1),

            ])
            ->columns(2)
            ->collapsible();
    }
}