<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load the roles relationship
        $this->record->load('roles');

        // Set the roles as an array of IDs for the CheckboxList
        $data['roles'] = $this->record->roles->pluck('id')->toArray();


        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Store roles data for later processing
        $this->rolesData = $data['roles'] ?? [];
        unset($data['roles']);

        // Remove password if empty to avoid overwriting with empty value
        if (empty($data['password'])) {
            unset($data['password']);
        }


        return $data;
    }

    protected function afterSave(): void
    {
        // Sync roles after saving the user
        if (isset($this->rolesData)) {
            // Convert role IDs to role names for syncRoles
            $roleNames = \Spatie\Permission\Models\Role::whereIn('id', $this->rolesData)
                ->pluck('name')
                ->toArray();

            $this->record->syncRoles($roleNames);
            $this->record->load('roles');
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    private $rolesData = [];
}
