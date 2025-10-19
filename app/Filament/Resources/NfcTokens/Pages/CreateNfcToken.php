<?php

namespace App\Filament\Resources\NfcTokens\Pages;

use App\Filament\Resources\NfcTokens\NfcTokenResource;
use App\Models\DynamicContent;
use Filament\Resources\Pages\CreateRecord;

class CreateNfcToken extends CreateRecord
{
    protected static string $resource = NfcTokenResource::class;

    /**
     * Hook que se ejecuta después de crear el NFC Token
     * Crea automáticamente el contenido dinámico asociado
     */
    protected function afterCreate(): void
    {
        $nfcToken = $this->record;

        // Crear contenido dinámico automáticamente
        DynamicContent::create([
            'content_id' => $nfcToken->token_id, // Usar el mismo UUID del token
            'type' => $nfcToken->content_type ?? 'PROFILE',
            'title' => $nfcToken->name ?? 'Contenido NFC',
            'description' => 'Contenido dinámico para ' . ($nfcToken->name ?? 'token NFC'),
            'data' => [],
            'is_active' => true,
            'status' => 'draft',
            'user_id' => $nfcToken->user_id,
            'nfc_token_id' => $nfcToken->id,
        ]);
    }
}
