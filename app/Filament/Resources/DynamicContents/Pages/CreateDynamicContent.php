<?php

namespace App\Filament\Resources\DynamicContents\Pages;

use App\Filament\Resources\DynamicContents\DynamicContentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDynamicContent extends CreateRecord
{
    protected static string $resource = DynamicContentResource::class;
}
