<?php

namespace App\Filament\Resources\UtilityPhones\Pages;

use App\Filament\Resources\UtilityPhones\UtilityPhoneResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUtilityPhone extends EditRecord
{
    protected static string $resource = UtilityPhoneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
