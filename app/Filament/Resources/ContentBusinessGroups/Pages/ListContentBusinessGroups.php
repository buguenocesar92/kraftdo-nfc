<?php

namespace App\Filament\Resources\ContentBusinessGroups\Pages;

use App\Filament\Resources\ContentBusinessGroups\ContentBusinessGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContentBusinessGroups extends ListRecords
{
    protected static string $resource = ContentBusinessGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
