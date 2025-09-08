<?php

namespace App\Filament\Resources\ContentTourists\Pages;

use App\Filament\Resources\ContentTourists\ContentTouristResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditContentTourist extends EditRecord
{
    protected static string $resource = ContentTouristResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
