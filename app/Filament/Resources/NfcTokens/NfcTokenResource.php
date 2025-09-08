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

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_any_nfc_token') || auth()->user()->can('view_nfc_token');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_nfc_token');
    }

    public static function canView($record): bool
    {
        return auth()->user()->can('view_nfc_token', $record) || auth()->user()->can('view_any_nfc_token');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('update_nfc_token', $record);
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_nfc_token', $record) || auth()->user()->can('delete_any_nfc_token');
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->can('delete_any_nfc_token');
    }

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
