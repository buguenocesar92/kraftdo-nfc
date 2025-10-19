<?php

namespace App\Filament\Components;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;

class MultimediaSection
{
    public static function make(): Section
    {
        return Section::make('Contenido Multimedia')
            ->schema([
                Group::make([
                    TextInput::make('video_url')
                        ->label('URL del Video')
                        ->url()
                        ->helperText('YouTube, Vimeo o enlaces directos'),

                    FileUpload::make('video_file')
                        ->label('Subir Video')
                        ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg', 'video/avi', 'video/mov'])
                        ->maxSize(100 * 1024) // 100MB
                        ->directory('multimedia/videos')
                        ->visibility('public')
                        ->helperText('Formatos: MP4, WebM, OGG, AVI, MOV. Máximo 100MB'),
                ])
                    ->columns(1)
                    ->columnSpan(1),

                Group::make([
                    TextInput::make('audio_url')
                        ->label('URL del Audio')
                        ->url()
                        ->helperText('Spotify, SoundCloud o enlaces directos'),

                    FileUpload::make('audio_file')
                        ->label('Subir Audio')
                        ->acceptedFileTypes(['audio/mp3', 'audio/wav', 'audio/ogg', 'audio/aac', 'audio/flac'])
                        ->maxSize(50 * 1024) // 50MB
                        ->directory('multimedia/audio')
                        ->visibility('public')
                        ->helperText('Formatos: MP3, WAV, OGG, AAC, FLAC. Máximo 50MB'),
                ])
                    ->columns(1)
                    ->columnSpan(1),

            ])
            ->columns(2)
            ->collapsible();
    }
}
