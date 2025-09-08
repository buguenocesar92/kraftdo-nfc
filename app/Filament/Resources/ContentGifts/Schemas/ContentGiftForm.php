<?php

namespace App\Filament\Resources\ContentGifts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ContentGiftForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('dynamic_content_id')
                    ->relationship('dynamicContent', 'title')
                    ->required(),
                TextInput::make('sender_name'),
                TextInput::make('recipient_name'),
                Textarea::make('message')
                    ->columnSpanFull(),
            ]);
    }
}
