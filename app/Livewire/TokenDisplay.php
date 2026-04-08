<?php

namespace App\Livewire;

use App\Services\TokenService;
use App\Services\ViewingLockService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.token')]
class TokenDisplay extends Component
{
    #[Locked]
    public string $tokenId;

    /** El request inicial no tiene Referer → scan físico NFC */
    #[Locked]
    public bool $isPhysical = false;

    /** Bloqueado por viewing lock (link compartido mientras alguien lo ve físicamente) */
    public bool $blocked = false;

    /** Token inexistente o inactivo */
    public bool $notFound = false;

    public function mount(
        string $tokenId,
        TokenService $tokenService,
        ViewingLockService $viewingLock,
    ): void {
        $this->tokenId    = $tokenId;
        $this->isPhysical = $viewingLock->isPhysicalScan(request());

        // Link compartido bloqueado mientras alguien lo está viendo físicamente
        if (! $this->isPhysical && $viewingLock->hasLock($tokenId)) {
            $this->blocked = true;
            return;
        }

        $data = $tokenService->getTokenWithContent($tokenId);

        if (! $data || ! $tokenService->validateToken($data)) {
            $this->notFound = true;
            return;
        }

        // Activar bandera en Redis cuando llega el scan físico
        if ($this->isPhysical) {
            $viewingLock->setLock($tokenId);
        }
    }

    /**
     * Renueva el viewing lock. Invocado por wire:poll cada 25s cuando es scan físico.
     */
    public function sendHeartbeat(ViewingLockService $viewingLock): void
    {
        if ($this->isPhysical) {
            $viewingLock->refreshLock($this->tokenId);
        }
    }

    public function render(TokenService $tokenService): View
    {
        $tokenData = null;

        if (! $this->blocked && ! $this->notFound) {
            $raw = $tokenService->getTokenWithContent($this->tokenId);

            if ($raw) {
                $tokenData = [
                    'token'          => $raw['token'],
                    'dynamicContent' => $raw['dynamicContent'],
                    'content'        => $raw['content'],
                    'contentType'    => $raw['token']->content_type,
                ];
            }
        }

        return view('livewire.token.display', compact('tokenData'));
    }
}
