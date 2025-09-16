<?php

namespace App\Filament\Resources\ContentEvents\Pages;

use App\Filament\Resources\ContentEvents\ContentEventResource;
use Filament\Resources\Pages\CreateRecord;

class CreateContentEvent extends CreateRecord
{
    protected static string $resource = ContentEventResource::class;
}
