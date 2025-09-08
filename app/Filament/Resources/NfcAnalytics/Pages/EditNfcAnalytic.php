<?php

namespace App\Filament\Resources\NfcAnalytics\Pages;

use App\Filament\Resources\NfcAnalytics\NfcAnalyticResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditNfcAnalytic extends EditRecord
{
    protected static string $resource = NfcAnalyticResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
