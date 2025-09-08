<?php

namespace App\Filament\Resources\NfcTokens\Pages;

use App\Filament\Resources\NfcTokens\NfcTokenResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNfcTokens extends ListRecords
{
    protected static string $resource = NfcTokenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
