@props([
    'content' => null,
    'theme' => 'default',
    'showAdminInfo' => false
])

<footer class="bg-kraftdo-dark border-t border-kraftdo-navy/20 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="md:col-span-2">
                <div class="flex items-center space-x-2 mb-4">
                    <div class="w-8 h-8 bg-gradient-to-r from-kraftdo-blue to-kraftdo-green rounded-full"></div>
                    <span class="text-2xl font-bold text-white">KRAFTDO</span>
                </div>
                <p class="text-gray-400 mb-4 max-w-md">
                    Transformamos ideas en soluciones digitales innovadoras que impulsan el crecimiento de tu negocio.
                </p>
                <div class="flex space-x-4">
                    <a href="https://www.instagram.com/kraftdonfc/" target="_blank" class="text-gray-400 hover:text-kraftdo-green transition-colors duration-300">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>
                </div>
            </div>
            
            <div>
                <h4 class="text-lg font-semibold text-white mb-4">Servicios</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-400 hover:text-kraftdo-green transition-colors duration-300">Desarrollo Web</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-kraftdo-green transition-colors duration-300">Apps Móviles</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-kraftdo-green transition-colors duration-300">E-commerce</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-kraftdo-green transition-colors duration-300">Consultoría</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="text-lg font-semibold text-white mb-4">Contacto</h4>
                <ul class="space-y-2">
                    <li class="text-gray-400">
                        <a href="mailto:contacto@kraftdo.com" class="hover:text-kraftdo-green transition-colors duration-300">
                            contacto@kraftdo.com
                        </a>
                    </li>
                    <li class="text-gray-400">
                        <a href="tel:+56934426430" class="hover:text-kraftdo-green transition-colors duration-300">
                            +56 9 3442 6430
                        </a>
                    </li>
                    <li class="text-gray-400">Machalí<br>Región de O'Higgins, Chile</li>
                </ul>
            </div>
        </div>
        
        <div class="border-t border-kraftdo-navy/20 mt-8 pt-8 text-center">
            <p class="text-gray-400">
                © {{ date('Y') }} KRAFTDO. Todos los derechos reservados.
            </p>
        </div>
    </div>


    @if($showAdminInfo && $content)
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            <div class="bg-kraftdo-navy/30 border border-kraftdo-green/20 rounded-2xl p-6 text-left">
                <h4 class="font-bold text-kraftdo-green mb-4 flex items-center gap-2">
                    <div class="bg-gradient-to-r from-kraftdo-blue to-kraftdo-green rounded-full p-2">
                        <i class="fas fa-cog text-white"></i>
                    </div>
                    Información de Administrador
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-gray-300">
                    <div class="bg-kraftdo-dark/50 rounded-lg p-3 border border-kraftdo-navy/30">
                        <strong class="text-kraftdo-blue">Content ID:</strong> <span class="text-white">{{ $content->content_id }}</span>
                    </div>
                    <div class="bg-kraftdo-dark/50 rounded-lg p-3 border border-kraftdo-navy/30">
                        <strong class="text-kraftdo-blue">Tipo:</strong> <span class="text-kraftdo-green">{{ $content->type }}</span>
                    </div>
                    <div class="bg-kraftdo-dark/50 rounded-lg p-3 border border-kraftdo-navy/30">
                        <strong class="text-kraftdo-blue">Título:</strong> <span class="text-white">{{ $content->title }}</span>
                    </div>
                    <div class="bg-kraftdo-dark/50 rounded-lg p-3 border border-kraftdo-navy/30">
                        <strong class="text-kraftdo-blue">Estado:</strong> 
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs {{ $content->is_active ? 'bg-kraftdo-green/20 text-kraftdo-green border border-kraftdo-green/30' : 'bg-red-500/20 text-red-400 border border-red-500/30' }}">
                            <i class="fas fa-{{ $content->is_active ? 'check-circle' : 'times-circle' }} mr-1"></i>
                            {{ $content->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                    <div class="bg-kraftdo-dark/50 rounded-lg p-3 border border-kraftdo-navy/30">
                        <strong class="text-kraftdo-blue">Creado:</strong> <span class="text-white">{{ $content->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="bg-kraftdo-dark/50 rounded-lg p-3 border border-kraftdo-navy/30">
                        <strong class="text-kraftdo-blue">Actualizado:</strong> <span class="text-white">{{ $content->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
                <div class="mt-4 bg-kraftdo-dark/50 rounded-lg p-3 border border-kraftdo-navy/30">
                    <strong class="text-kraftdo-blue">URL para editar:</strong>
                    <code class="block mt-2 bg-kraftdo-navy/50 text-kraftdo-green px-3 py-2 rounded text-xs break-all border border-kraftdo-green/20">
                        /nfc?TYPE={{ $content->type }}&ID={{ $content->content_id }}&admin=1
                    </code>
                </div>
            </div>
        </div>
    @endif
</footer> 