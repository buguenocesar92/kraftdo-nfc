<?php

namespace App\Filament\Resources\DynamicContents\Schemas;

use App\Models\DynamicContent;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DynamicContentFormSimple
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('content_id')
                    ->required()
                    ->default(fn () => (string) \Illuminate\Support\Str::uuid()),
                Select::make('type')
                    ->options(DynamicContent::TYPES)
                    ->required(),
                TextInput::make('gift_subtype')
                    ->label('Subtipo (solo para regalos)'),
                TextInput::make('tier')
                    ->required()
                    ->default('sweet'),
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('image_url')
                    ->label('URL de la imagen'),
                Toggle::make('is_active')
                    ->required()
                    ->default(true),
                Select::make('status')
                    ->options([
                        'draft' => 'Borrador',
                        'published' => 'Publicado',
                        'paused' => 'Pausado',
                    ])
                    ->required()
                    ->default('draft'),
                Textarea::make('data')
                    ->label('Datos adicionales (JSON)')
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
                    ->helperText('Formato JSON para todos los datos específicos del tipo de contenido.'),
            ]);
    }
}
