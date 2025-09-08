<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extract permissions data before creating the role
        $this->permissionsData = $data['permissions'] ?? [];
        unset($data['permissions']);
        
        return $data;
    }

    protected function afterCreate(): void
    {
        // Assign permissions after creating the role
        if (!empty($this->permissionsData)) {
            // Convert permission IDs to permission names for syncPermissions
            $permissionNames = \Spatie\Permission\Models\Permission::whereIn('id', $this->permissionsData)
                ->pluck('name')
                ->toArray();
            
            $this->record->syncPermissions($permissionNames);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    private $permissionsData = [];
}
