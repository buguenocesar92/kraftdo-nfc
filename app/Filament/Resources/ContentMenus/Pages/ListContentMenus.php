<?php

namespace App\Filament\Resources\ContentMenus\Pages;

use App\Filament\Resources\ContentMenus\ContentMenuResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContentMenus extends ListRecords
{
    protected static string $resource = ContentMenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
