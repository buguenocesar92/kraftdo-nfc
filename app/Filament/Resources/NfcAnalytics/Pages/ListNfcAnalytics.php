<?php

namespace App\Filament\Resources\NfcAnalytics\Pages;

use App\Filament\Resources\NfcAnalytics\NfcAnalyticResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNfcAnalytics extends ListRecords
{
    protected static string $resource = NfcAnalyticResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
