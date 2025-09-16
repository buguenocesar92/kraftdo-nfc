<?php

namespace App\Filament\Resources\ContentProducts\Pages;

use App\Filament\Resources\ContentProducts\ContentProductResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditContentProduct extends EditRecord
{
    protected static string $resource = ContentProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
