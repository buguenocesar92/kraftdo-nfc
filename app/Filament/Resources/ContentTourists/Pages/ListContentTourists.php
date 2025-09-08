<?php

namespace App\Filament\Resources\ContentTourists\Pages;

use App\Filament\Resources\ContentTourists\ContentTouristResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContentTourists extends ListRecords
{
    protected static string $resource = ContentTouristResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
