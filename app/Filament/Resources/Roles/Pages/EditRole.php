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

        // Set the permissions as an array of IDs for the CheckboxList
        $data['permissions'] = $this->record->permissions->pluck('id')->toArray();


        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Store permissions data for later processing
        $this->permissionsData = $data['permissions'] ?? [];
        unset($data['permissions']);


        return $data;
    }

    protected function afterSave(): void
    {
        // Sync permissions after saving the role
        if (isset($this->permissionsData)) {
            // Convert permission IDs to permission names for syncPermissions
            $permissionNames = \Spatie\Permission\Models\Permission::whereIn('id', $this->permissionsData)
                ->pluck('name')
                ->toArray();

            $this->record->syncPermissions($permissionNames);
            $this->record->load('permissions');
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    private $permissionsData = [];
}
