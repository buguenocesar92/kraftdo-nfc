<?php

namespace App\Filament\Resources\ContentMultimedia\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ContentMultimediaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('dynamic_content_id')
                    ->relationship(
                        name: 'dynamicContent',
                        titleAttribute: 'title'
                        // Multimedia puede estar relacionado con cualquier tipo
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->title} ({$record->type}) - {$record->content_id}")
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('video_url')
                    ->url(),
                TextInput::make('video_type'),
                TextInput::make('audio_url')
                    ->url(),
                TextInput::make('audio_type'),
                Textarea::make('gallery_images')
                    ->columnSpanFull(),
                Textarea::make('settings')
                    ->columnSpanFull(),
            ]);
    }
}
