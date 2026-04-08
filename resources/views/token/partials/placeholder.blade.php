{{-- Placeholder para tipos de contenido sin vista Blade todavía --}}
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200 p-6">
    <div class="max-w-sm w-full bg-white rounded-3xl shadow-lg p-8 text-center">
        <div class="text-5xl mb-4">🔧</div>
        <h1 class="text-xl font-bold text-gray-800 mb-2">
            {{ $token->name ?? 'Token NFC' }}
        </h1>
        <p class="text-gray-500 text-sm mb-4">
            Contenido de tipo <span class="font-semibold text-gray-700">{{ $contentType }}</span>
        </p>
        <p class="text-gray-400 text-xs">
            La visualización web para este tipo de contenido estará disponible pronto.
        </p>
        <div class="mt-6 pt-4 border-t border-gray-100">
            <p class="text-xs text-gray-300">Powered by <span class="font-semibold">KraftDo</span></p>
        </div>
    </div>
</div>
