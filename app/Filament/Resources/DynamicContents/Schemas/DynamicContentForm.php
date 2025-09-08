<?php

namespace App\Filament\Resources\DynamicContents\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\KeyValue;
use Filament\Schemas\Schema;

class DynamicContentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('content_id')
                    ->required(),
                TextInput::make('type')
                    ->required(),
                TextInput::make('gift_subtype'),
                TextInput::make('tier')
                    ->required()
                    ->default('sweet'),
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Textarea::make('data')
                    ->label('Datos JSON')
                    ->required()
                    ->columnSpanFull()
                    ->rows(10)
                    ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $state)
                    ->dehydrateStateUsing(function ($state) {
                        if (is_string($state)) {
                            $decoded = json_decode($state, true);
                            return json_last_error() === JSON_ERROR_NONE ? $decoded : $state;
                        }
                        return $state;
                    })
                    ->helperText('Formato JSON válido. Se mostrará formateado al cargar.'),
                FileUpload::make('image_url')
                    ->image(),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('draft'),
                DateTimePicker::make('published_at'),
                DateTimePicker::make('last_draft_update'),
                TextInput::make('post_publish_modifications')
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('published_snapshot')
                    ->label('Snapshot de Publicación (JSON)')
                    ->columnSpanFull()
                    ->rows(6)
                    ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $state)
                    ->dehydrateStateUsing(function ($state) {
                        if (is_string($state) && !empty($state)) {
                            $decoded = json_decode($state, true);
                            return json_last_error() === JSON_ERROR_NONE ? $decoded : $state;
                        }
                        return $state;
                    })
                    ->helperText('Formato JSON del contenido cuando fue publicado.')
                    ->disabled()
                    ->dehydrated(false),
                Select::make('user_id')
                    ->relationship('user', 'name'),
                Select::make('nfc_token_id')
                    ->relationship('nfcToken', 'name'),
            ]);
    }
}
