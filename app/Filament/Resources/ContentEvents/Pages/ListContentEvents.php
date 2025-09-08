<?php

namespace App\Filament\Resources\ContentEvents\Pages;

use App\Filament\Resources\ContentEvents\ContentEventResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContentEvents extends ListRecords
{
    protected static string $resource = ContentEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
