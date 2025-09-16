<?php

namespace App\Filament\Resources\ContentGifts\Pages;

use App\Filament\Resources\ContentGifts\ContentGiftResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContentGifts extends ListRecords
{
    protected static string $resource = ContentGiftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
