<?php

namespace App\Filament\Resources\NfcTokens\Schemas;

use App\Models\DynamicContent;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class NfcTokenForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('token_id')
                    ->label('Token UUID')
                    ->disabled()
                    ->dehydrated()
                    ->placeholder('Se genera automáticamente'),
                Select::make('user_id')
                    ->relationship('user', 'name'),
                TextInput::make('name'),
                Select::make('content_type')
                    ->label('Tipo de Contenido')
                    ->options(DynamicContent::getActiveTypes())
                    ->required()
                    ->searchable()
                    ->helperText('Solo se muestran tipos con recursos especializados implementados'),
                TextInput::make('customization_plan')
                    ->required()
                    ->default('BASIC')
                    ->hidden()
                    ->dehydrated(),
                TextInput::make('purchase_price')
                    ->numeric()
                    ->hidden()
                    ->dehydrated(),
                DateTimePicker::make('purchased_at')
                    ->hidden()
                    ->dehydrated(),
                Textarea::make('purchase_notes')
                    ->columnSpanFull()
                    ->hidden()
                    ->dehydrated(),
                TextInput::make('purchase_currency')
                    ->required()
                    ->default('USD')
                    ->hidden()
                    ->dehydrated(),
                TextInput::make('cost_per_view')
                    ->numeric()
                    ->hidden()
                    ->dehydrated(),
                TextInput::make('total_investment_views')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->hidden()
                    ->dehydrated(),
                Toggle::make('is_active')
                    ->required()
                    ->default(true),
                DateTimePicker::make('last_used_at')
                    ->hidden()
                    ->dehydrated(),
            ]);
    }
}
