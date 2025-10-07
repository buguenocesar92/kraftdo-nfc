<?php

namespace App\Filament\Resources\BusStops\Pages;

use App\Filament\Resources\BusStops\BusStopResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBusStop extends EditRecord
{
    protected static string $resource = BusStopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
