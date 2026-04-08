<div
    @if($isPhysical)
        wire:poll.25000ms="sendHeartbeat"
    @endif
>
    {{-- ── BLOQUEADO: alguien lo está viendo físicamente ── --}}
    @if($blocked)
        <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-800 to-slate-900 p-6">
            <div class="max-w-sm w-full bg-white/10 backdrop-blur rounded-2xl p-8 text-center text-white">
                <div class="text-5xl mb-4">🖼️</div>
                <h1 class="text-xl font-bold mb-2">Cuadro en uso</h1>
                <p class="text-slate-300 text-sm leading-relaxed">
                    Este cuadro está siendo visualizado en este momento.<br>
                    Inténtalo en unos segundos.
                </p>
                <p class="mt-4 text-xs text-slate-400">Reintentar en 30 segundos</p>
            </div>
        </div>

    {{-- ── NO ENCONTRADO ── --}}
    @elseif($notFound)
        <div class="min-h-screen flex items-center justify-center bg-gray-100 p-6">
            <div class="max-w-sm w-full bg-white rounded-2xl shadow p-8 text-center">
                <div class="text-5xl mb-4">🔍</div>
                <h1 class="text-xl font-bold text-gray-800 mb-2">Token no encontrado</h1>
                <p class="text-gray-500 text-sm">
                    El token no existe, fue desactivado o no tiene contenido aún.
                </p>
            </div>
        </div>

    {{-- ── CONTENIDO ── --}}
    @elseif($tokenData)
        @php
            $token          = $tokenData['token'];
            $dynamicContent = $tokenData['dynamicContent'];
            $content        = $tokenData['content'];
            $contentType    = $tokenData['contentType'];
        @endphp

        @if($contentType === 'PROFILE')
            @include('token.partials.profile', [
                'token'             => $token,
                'dynamicContent'    => $dynamicContent,
                'contentProfile'    => $content['profile'] ?? null,
                'contentMultimedia' => $content['multimedia'] ?? null,
            ])

        @elseif($contentType === 'GIFT')
            @include('token.partials.gift', [
                'token'             => $token,
                'dynamicContent'    => $dynamicContent,
                'contentGift'       => $content['gift'] ?? null,
                'contentMultimedia' => $content['multimedia'] ?? null,
            ])

        @else
            @include('token.partials.placeholder', [
                'contentType' => $contentType,
                'token'       => $token,
            ])
        @endif
    @endif
</div>
