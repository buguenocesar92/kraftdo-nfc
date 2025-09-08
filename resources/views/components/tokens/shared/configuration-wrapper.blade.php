@props([
    'token',
    'content',
    'title' => null,
    'subtitle' => null
])

<x-layout.base 
    :title="($title ?? 'Configurar') . ' ' . $token->name . ' - Sistema NFC'"
    :emoji1="'🔗'"
    :emoji2="'✨'"
>
    <x-nfc.token-header 
        :token="$token"
        :title="$title ?? 'Configurar: ' . $token->name"
        :subtitle="$subtitle ?? 'Personaliza el contenido de tu chip NFC'"
    />

    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Información del Chip -->
        <x-nfc.token-info-card :token="$token" :content="$content" />

        <!-- Botones de Estado del Contenido -->
        @if($content)
            <x-content-status-buttons :content="$content" />
        @endif

        {{ $slot }}
    </div>

    <x-slot name="scripts">
        {{ $scripts ?? '' }}
    </x-slot>
</x-layout.base>