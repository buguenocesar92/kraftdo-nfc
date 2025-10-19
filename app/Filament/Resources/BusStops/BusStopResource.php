<?php

namespace App\Filament\Resources\BusStops;

use App\Filament\Resources\BusStops\Pages\CreateBusStop;
use App\Filament\Resources\BusStops\Pages\EditBusStop;
use App\Filament\Resources\BusStops\Pages\ListBusStops;
use App\Filament\Resources\BusStops\Schemas\BusStopForm;
use App\Filament\Resources\BusStops\Tables\BusStopsTable;
use App\Models\BusStop;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BusStopResource extends Resource
{
    protected static ?string $model = BusStop::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;
    
    public static function canAccess(): bool
    {
        return auth()->user()?->can('view_bus_stops') ?? false;
    }
    
    public static function getNavigationGroup(): ?string
    {
        return 'Transporte Público';
    }
    
    public static function getNavigationLabel(): string
    {
        return 'Paraderos';
    }
    
    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function form(Schema $schema): Schema
    {
        return BusStopForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BusStopsTable::configure($table);
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
            'index' => ListBusStops::route('/'),
            'create' => CreateBusStop::route('/create'),
            'edit' => EditBusStop::route('/{record}/edit'),
        ];
    }
}
