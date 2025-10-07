<?php

namespace App\Filament\Resources\UtilityPhones\Pages;

use App\Filament\Resources\UtilityPhones\UtilityPhoneResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUtilityPhones extends ListRecords
{
    protected static string $resource = UtilityPhoneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
