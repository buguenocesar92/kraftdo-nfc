<?php

namespace App\Filament\Resources\ContentBusinesses\Pages;

use App\Filament\Resources\ContentBusinesses\ContentBusinessResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContentBusinesses extends ListRecords
{
    protected static string $resource = ContentBusinessResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}