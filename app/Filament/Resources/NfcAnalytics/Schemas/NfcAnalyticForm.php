<?php

namespace App\Filament\Resources\NfcAnalytics\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class NfcAnalyticForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('content_id')
                    ->required(),
                TextInput::make('content_type')
                    ->required(),
                Select::make('nfc_token_id')
                    ->relationship('nfcToken', 'name'),
                TextInput::make('ip_address'),
                Textarea::make('user_agent')
                    ->columnSpanFull(),
                TextInput::make('country'),
                TextInput::make('city'),
                TextInput::make('device_type'),
                TextInput::make('browser'),
                TextInput::make('referrer'),
                Toggle::make('is_unique_visit')
                    ->required(),
                DateTimePicker::make('accessed_at')
                    ->required(),
            ]);
    }
}
