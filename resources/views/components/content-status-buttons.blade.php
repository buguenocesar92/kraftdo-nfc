@props(['content'])

<div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
    <!-- Status actual -->
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-2">
            @if($content->isDraft())
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                    <i class="fas fa-edit mr-1"></i>
                    Borrador
                </span>
                <span class="text-sm text-gray-500">Modificaciones ilimitadas</span>
            @elseif($content->isPublished())
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <i class="fas fa-eye mr-1"></i>
                    Publicado
                </span>
            @elseif($content->isPaused())
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                    <i class="fas fa-pause mr-1"></i>
                    Pausado
                </span>
                <span class="text-sm text-gray-500">No visible públicamente</span>
            @endif
        </div>
        
        @if($content->published_at)
            <div class="text-right text-sm text-gray-500">
                <div>Publicado: {{ $content->published_at ? $content->published_at->format('d/m/Y H:i') : 'N/A' }}</div>
                <div>Hace {{ $content->getDaysSincePublished() }} días</div>
            </div>
        @endif
    </div>
    
    <!-- Botones de acción -->
    <div class="flex flex-wrap gap-2">
        @if($content->isDraft())
            <!-- Botón principal: Publicar -->
            <form action="{{ route('content.publish', $content) }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                        onclick="return confirm('¿Estás seguro de que quieres publicar este contenido? Una vez publicado, solo podrás hacer modificaciones limitadas y no podrás volver a modo borrador.')"
                        class="kraftdo-gradient text-white px-4 sm:px-8 py-3 rounded-xl hover:shadow-lg transition-all duration-200 transform hover:scale-105 font-semibold text-center text-sm sm:text-base">
                    <i class="fas fa-rocket mr-2"></i>
                    Publicar
                </button>
            </form>
            
        @elseif($content->isPublished())
            <!-- Botón: Pausar -->
            <form action="{{ route('content.pause', $content) }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                        onclick="return confirm('¿Estás seguro? El contenido dejará de ser visible públicamente, pero podrás reactivarlo después.')"
                        class="bg-orange-500 text-white px-4 sm:px-8 py-3 rounded-xl hover:shadow-lg transition-all duration-200 transform hover:scale-105 font-semibold text-center text-sm sm:text-base">
                    <i class="fas fa-pause mr-2"></i>
                    Pausar Temporalmente
                </button>
            </form>
            
        @elseif($content->isPaused())
            <!-- Botón: Reactivar -->
            <form action="{{ route('content.unpause', $content) }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                        class="kraftdo-gradient text-white px-4 sm:px-8 py-3 rounded-xl hover:shadow-lg transition-all duration-200 transform hover:scale-105 font-semibold text-center text-sm sm:text-base">
                    <i class="fas fa-play mr-2"></i>
                    Reactivar Contenido
                </button>
            </form>
        @endif
        
        <!-- Link público (siempre visible) -->
        <div class="flex items-center space-x-2 ml-auto">
            <span class="text-sm text-gray-500">Link público:</span>
            @if($content->isPubliclyAccessible())
                <a href="{{ url('/nfc?TYPE=' . $content->type . '&ID=' . $content->nfcToken->token_id) }}" 
                   target="_blank"
                   class="text-blue-600 hover:text-blue-800 font-medium">
                    <i class="fas fa-external-link-alt mr-1"></i>
                    Ver Regalo
                </a>
            @else
                <span class="text-gray-400 italic">
                    <i class="fas fa-lock mr-1"></i>
                    Inactivo
                </span>
            @endif
        </div>
    </div>
    
    
    @if($content->isDraft())
        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex">
                <i class="fas fa-info-circle text-blue-600 mr-2 mt-0.5"></i>
                <div>
                    <p class="text-sm text-blue-800 font-medium">
                        Modo Borrador Activo
                    </p>
                    <p class="text-xs text-blue-700 mt-1">
                        Puedes modificar ilimitadamente. Una vez que publiques, las modificaciones serán limitadas.
                    </p>
                </div>
            </div>
        </div>
    @elseif($content->isPublished())
        <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex">
                <i class="fas fa-check-circle text-green-600 mr-2 mt-0.5"></i>
                <div>
                    <p class="text-sm text-green-800 font-medium">
                        Contenido Publicado
                    </p>
                    <p class="text-xs text-green-700 mt-1">
                        Tu contenido es visible públicamente. Puedes editarlo o pausarlo temporalmente.
                    </p>
                </div>
            </div>
        </div>
    @elseif($content->isPaused())
        <div class="mt-4 p-3 bg-orange-50 border border-orange-200 rounded-lg">
            <div class="flex">
                <i class="fas fa-pause-circle text-orange-600 mr-2 mt-0.5"></i>
                <div>
                    <p class="text-sm text-orange-800 font-medium">
                        Contenido Pausado
                    </p>
                    <p class="text-xs text-orange-700 mt-1">
                        Tu contenido no es visible públicamente. Puedes reactivarlo cuando quieras.
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>

 