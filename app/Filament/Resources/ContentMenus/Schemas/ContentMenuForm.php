<?php

namespace App\Filament\Resources\ContentMenus\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ContentMenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('dynamic_content_id')
                    ->relationship('dynamicContent', 'title')
                    ->required(),
                TextInput::make('restaurant_name'),
                TextInput::make('restaurant_phone')
                    ->tel(),
                Textarea::make('restaurant_address')
                    ->columnSpanFull(),
                TextInput::make('restaurant_hours'),
            ]);
    }
}
