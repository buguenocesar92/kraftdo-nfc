<?php

namespace App\Filament\Resources\NfcTokens\Pages;

use App\Filament\Resources\NfcTokens\NfcTokenResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditNfcToken extends EditRecord
{
    protected static string $resource = NfcTokenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
