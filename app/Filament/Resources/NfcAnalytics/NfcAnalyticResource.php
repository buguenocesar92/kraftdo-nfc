<?php

namespace App\Filament\Resources\NfcAnalytics;

use App\Filament\Resources\NfcAnalytics\Pages\CreateNfcAnalytic;
use App\Filament\Resources\NfcAnalytics\Pages\EditNfcAnalytic;
use App\Filament\Resources\NfcAnalytics\Pages\ListNfcAnalytics;
use App\Filament\Resources\NfcAnalytics\Schemas\NfcAnalyticForm;
use App\Filament\Resources\NfcAnalytics\Tables\NfcAnalyticsTable;
use App\Models\NfcAnalytic;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NfcAnalyticResource extends Resource
{
    protected static ?string $model = NfcAnalytic::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return NfcAnalyticForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NfcAnalyticsTable::configure($table);
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
            'index' => ListNfcAnalytics::route('/'),
            'create' => CreateNfcAnalytic::route('/create'),
            'edit' => EditNfcAnalytic::route('/{record}/edit'),
        ];
    }
}
