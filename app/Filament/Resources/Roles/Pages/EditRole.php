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
        // Cargar permisos solo si no están cargados (evitar duplicación)
        if (! $this->record->relationLoaded('permissions')) {
            $this->record->load('permissions');
        }

        // Configurar permisos como array de IDs para CheckboxList
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
        // Sincronizar permisos después de guardar el rol
        if (isset($this->permissionsData)) {
            // Usar consulta directa sin crear objetos Eloquent
            $permissionNames = \DB::table('permissions')
                ->whereIn('id', $this->permissionsData)
                ->pluck('name')
                ->toArray();

            // Sincronizar permisos (esto ya refresca la relación internamente)
            $this->record->syncPermissions($permissionNames);

            // NO volver a cargar - syncPermissions ya actualiza la relación
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    private $permissionsData = [];
}
