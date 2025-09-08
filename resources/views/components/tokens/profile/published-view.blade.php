@props(['token', 'content'])

<div class="bg-white rounded-lg shadow-sm border p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">
        Vista Publicada - Token de Perfil
    </h3>
    
    @if($content)
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Título</label>
                <p class="text-gray-900">{{ $content->title ?? 'Sin título' }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Descripción</label>
                <p class="text-gray-600">{{ $content->description ?? 'Sin descripción' }}</p>
            </div>
            
            @if($content->data)
                @if(isset($content->data['name']))
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                    <p class="text-gray-900">{{ $content->data['name'] }}</p>
                </div>
                @endif
                
                @if(isset($content->data['email']))
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <p class="text-gray-900">{{ $content->data['email'] }}</p>
                </div>
                @endif
                
                @if(isset($content->data['phone']))
                <div>
                    <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                    <p class="text-gray-900">{{ $content->data['phone'] }}</p>
                </div>
                @endif
            @endif
            
            <div class="mt-4 pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-500">
                    <strong>Token:</strong> {{ $token->token_id }}<br>
                    <strong>Estado:</strong> 
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                        {{ $content->publication_status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ ucfirst($content->publication_status) }}
                    </span>
                </p>
            </div>
        </div>
    @else
        <div class="text-center py-8">
            <div class="text-gray-400 mb-2">
                <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-1">Sin contenido</h3>
            <p class="text-gray-500">Este token no tiene contenido publicado aún.</p>
        </div>
    @endif
</div> 