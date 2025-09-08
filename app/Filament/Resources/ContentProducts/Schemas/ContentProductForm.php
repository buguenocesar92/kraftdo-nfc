<?php

namespace App\Filament\Resources\ContentProducts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ContentProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('dynamic_content_id')
                    ->relationship('dynamicContent', 'title')
                    ->required(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('currency')
                    ->required()
                    ->default('USD'),
                TextInput::make('sku')
                    ->label('SKU'),
                TextInput::make('stock')
                    ->numeric(),
                Toggle::make('in_stock')
                    ->required(),
                TextInput::make('brand'),
                Textarea::make('specifications')
                    ->columnSpanFull(),
                TextInput::make('purchase_url')
                    ->url(),
            ]);
    }
}
