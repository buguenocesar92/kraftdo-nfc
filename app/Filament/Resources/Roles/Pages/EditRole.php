<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load the permissions relationship
        $this->record->load('permissions');
        
        // Set the permissions as an array of IDs
        $data['permissions'] = $this->record->permissions->pluck('id')->toArray();
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remove permissions from data as they're handled by relationship
        $this->permissionsData = $data['permissions'] ?? [];
        unset($data['permissions']);
        
        return $data;
    }

    protected function afterSave(): void
    {
        // Sync permissions after saving the role
        if (isset($this->permissionsData)) {
            $this->record->syncPermissions($this->permissionsData);
        }
    }

    private $permissionsData = [];
}
