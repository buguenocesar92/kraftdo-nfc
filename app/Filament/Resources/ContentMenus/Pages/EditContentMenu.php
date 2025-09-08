<?php

namespace App\Filament\Resources\ContentMenus\Pages;

use App\Filament\Resources\ContentMenus\ContentMenuResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditContentMenu extends EditRecord
{
    protected static string $resource = ContentMenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
