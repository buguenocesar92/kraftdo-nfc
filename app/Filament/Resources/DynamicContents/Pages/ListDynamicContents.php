<?php

namespace App\Filament\Resources\DynamicContents\Pages;

use App\Filament\Resources\DynamicContents\DynamicContentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDynamicContents extends ListRecords
{
    protected static string $resource = DynamicContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
