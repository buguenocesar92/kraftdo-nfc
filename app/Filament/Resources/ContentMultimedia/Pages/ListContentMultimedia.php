<?php

namespace App\Filament\Resources\ContentMultimedia\Pages;

use App\Filament\Resources\ContentMultimedia\ContentMultimediaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContentMultimedia extends ListRecords
{
    protected static string $resource = ContentMultimediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
