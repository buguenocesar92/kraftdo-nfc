<?php

namespace App\Filament\Resources\ContentProducts\Schemas;

use App\Models\DynamicContent;
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
                // Campo oculto - se crea automáticamente
                // Select::make('dynamic_content_id')
                //     ->relationship('dynamicContent', 'title')
                //     ->label('Contenido Dinámico Asociado')
                //     ->disabled()
                //     ->helperText('Se crea automáticamente con los datos del producto'),
                
                TextInput::make('name')
                    ->label('Nombre del Producto')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
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
