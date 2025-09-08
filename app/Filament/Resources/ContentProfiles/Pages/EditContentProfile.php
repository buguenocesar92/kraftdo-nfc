<?php

namespace App\Filament\Resources\ContentProfiles\Pages;

use App\Filament\Resources\ContentProfiles\ContentProfileResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditContentProfile extends EditRecord
{
    protected static string $resource = ContentProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
