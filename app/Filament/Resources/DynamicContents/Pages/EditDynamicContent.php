<?php

namespace App\Filament\Resources\DynamicContents\Pages;

use App\Filament\Resources\DynamicContents\DynamicContentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDynamicContent extends EditRecord
{
    protected static string $resource = DynamicContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
