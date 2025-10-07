<?php

namespace App\Filament\Resources\BusStops\Pages;

use App\Filament\Resources\BusStops\BusStopResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBusStops extends ListRecords
{
    protected static string $resource = BusStopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
