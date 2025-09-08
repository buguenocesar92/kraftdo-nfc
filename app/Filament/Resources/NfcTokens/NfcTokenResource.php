<?php

namespace App\Filament\Resources\NfcTokens;

use App\Filament\Resources\NfcTokens\Pages\CreateNfcToken;
use App\Filament\Resources\NfcTokens\Pages\EditNfcToken;
use App\Filament\Resources\NfcTokens\Pages\ListNfcTokens;
use App\Filament\Resources\NfcTokens\Schemas\NfcTokenForm;
use App\Filament\Resources\NfcTokens\Tables\NfcTokensTable;
use App\Models\NfcToken;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NfcTokenResource extends Resource
{
    protected static ?string $model = NfcToken::class;

    protected static ?string $navigationLabel = 'Chips NFC';
    
    protected static ?string $modelLabel = 'Chip NFC';
    
    protected static ?string $pluralModelLabel = 'Chips NFC';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return NfcTokenForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NfcTokensTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNfcTokens::route('/'),
            'create' => CreateNfcToken::route('/create'),
            'edit' => EditNfcToken::route('/{record}/edit'),
        ];
    }
}
