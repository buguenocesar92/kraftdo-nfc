<?php

namespace App\Filament\Components;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;

class MultimediaSection
{
    public static function make(): Section
    {
        return Section::make('Contenido Multimedia')
            ->schema([
                TextInput::make('dynamicContent.multimedia.video_url')
                    ->label('URL del Video')
                    ->url(),
                Select::make('dynamicContent.multimedia.video_type')
                    ->label('Tipo de Video')
                    ->options([
                        'file_upload' => 'Archivo subido',
                        'youtube' => 'YouTube',
                        'vimeo' => 'Vimeo',
                        'direct' => 'URL directa'
                    ]),
                TextInput::make('dynamicContent.multimedia.audio_url')
                    ->label('URL del Audio')
                    ->url(),
                Select::make('dynamicContent.multimedia.audio_type')
                    ->label('Tipo de Audio')
                    ->options([
                        'file_upload' => 'Archivo subido',
                        'youtube_music' => 'YouTube Music',
                        'spotify' => 'Spotify',
                        'soundcloud' => 'SoundCloud',
                        'direct' => 'URL directa'
                    ]),
                Textarea::make('dynamicContent.multimedia.gallery_images')
                    ->label('URLs de Galería (JSON)')
                    ->rows(4)
                    ->helperText('Formato JSON: ["url1", "url2", "url3"]')
                    ->columnSpanFull(),
            ])
            ->columns(2)
            ->collapsible();
    }
}