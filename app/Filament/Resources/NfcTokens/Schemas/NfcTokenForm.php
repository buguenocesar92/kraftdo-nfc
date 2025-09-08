<?php

namespace App\Filament\Resources\NfcTokens\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
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
                TextInput::make('content_type'),
                TextInput::make('customization_plan')
                    ->required()
                    ->default('BASIC'),
                TextInput::make('purchase_price')
                    ->numeric(),
                DateTimePicker::make('purchased_at'),
                Textarea::make('purchase_notes')
                    ->columnSpanFull(),
                TextInput::make('purchase_currency')
                    ->required()
                    ->default('USD'),
                TextInput::make('cost_per_view')
                    ->numeric(),
                TextInput::make('total_investment_views')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->required(),
                DateTimePicker::make('last_used_at'),
            ]);
    }
}
