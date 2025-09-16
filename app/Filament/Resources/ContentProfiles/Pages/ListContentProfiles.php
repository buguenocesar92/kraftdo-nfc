<?php

namespace App\Filament\Resources\ContentProfiles\Pages;

use App\Filament\Resources\ContentProfiles\ContentProfileResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContentProfiles extends ListRecords
{
    protected static string $resource = ContentProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
