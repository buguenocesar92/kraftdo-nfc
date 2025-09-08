<?php

namespace App\Filament\Resources\ContentGifts\Pages;

use App\Filament\Resources\ContentGifts\ContentGiftResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditContentGift extends EditRecord
{
    protected static string $resource = ContentGiftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
