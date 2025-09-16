<?php

namespace App\Filament\Resources\ContentEvents\Pages;

use App\Filament\Resources\ContentEvents\ContentEventResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditContentEvent extends EditRecord
{
    protected static string $resource = ContentEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
