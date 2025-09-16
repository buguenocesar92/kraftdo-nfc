<?php

namespace App\Filament\Resources\ContentProducts\Pages;

use App\Filament\Resources\ContentProducts\ContentProductResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContentProducts extends ListRecords
{
    protected static string $resource = ContentProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
