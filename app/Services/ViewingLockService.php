<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ViewingLockService
{
    public const VIEWING_TTL = 30; // segundos
    private const LOCK_PREFIX = 'viewing:';

    /**
     * Un request sin Referer (o vacío) es un scan físico NFC.
     * WhatsApp, Chrome, etc. siempre envían Referer.
     */
    public function isPhysicalScan(Request $request): bool
    {
        return empty($request->header('Referer'));
    }

    /**
     * Activa o renueva la bandera de visualización activa.
     */
    public function setLock(string $tokenId): void
    {
        Cache::put(self::LOCK_PREFIX . $tokenId, true, self::VIEWING_TTL);
    }

    /**
     * Verifica si hay una visualización activa para el token.
     */
    public function hasLock(string $tokenId): bool
    {
        return Cache::has(self::LOCK_PREFIX . $tokenId);
    }

    /**
     * Renueva el TTL si la bandera existe. Retorna true si se renovó.
     */
    public function refreshLock(string $tokenId): bool
    {
        if (! $this->hasLock($tokenId)) {
            return false;
        }

        $this->setLock($tokenId);

        return true;
    }

    /**
     * Elimina la bandera (útil para tests).
     */
    public function clearLock(string $tokenId): void
    {
        Cache::forget(self::LOCK_PREFIX . $tokenId);
    }
}
