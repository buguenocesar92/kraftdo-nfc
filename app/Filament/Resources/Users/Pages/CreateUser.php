<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extract roles data before creating the user
        $this->rolesData = $data['roles'] ?? [];
        unset($data['roles']);

        return $data;
    }

    protected function afterCreate(): void
    {
        // Assign roles after creating the user
        if (! empty($this->rolesData)) {
            // Convert role IDs to role names for syncRoles
            $roleNames = \Spatie\Permission\Models\Role::whereIn('id', $this->rolesData)
                ->pluck('name')
                ->toArray();

            $this->record->syncRoles($roleNames);
        }
    }

    private $rolesData = [];
}
