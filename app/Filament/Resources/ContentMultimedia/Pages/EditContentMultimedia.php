<?php

namespace App\Filament\Resources\ContentMultimedia\Pages;

use App\Filament\Resources\ContentMultimedia\ContentMultimediaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditContentMultimedia extends EditRecord
{
    protected static string $resource = ContentMultimediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
