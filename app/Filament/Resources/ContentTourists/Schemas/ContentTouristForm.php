<?php

namespace App\Filament\Resources\ContentTourists\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ContentTouristForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('dynamic_content_id')
                    ->relationship('dynamicContent', 'title')
                    ->required(),
                TextInput::make('location_name')
                    ->required(),
                Textarea::make('location_address')
                    ->columnSpanFull(),
                TextInput::make('latitude')
                    ->numeric(),
                TextInput::make('longitude')
                    ->numeric(),
                TextInput::make('opening_hours'),
                TextInput::make('entrance_fee')
                    ->numeric(),
                TextInput::make('fee_currency')
                    ->required()
                    ->default('USD'),
                TextInput::make('website_url')
                    ->url(),
                TextInput::make('phone')
                    ->tel(),
            ]);
    }
}
