@props(['content'])

<!-- Redes Sociales -->
<div class="bg-white/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-200/50">
    <div class="mb-6">
        <p class="text-sm font-semibold text-kraftdo-navy">
            <i class="fas fa-share-alt text-kraftdo-blue mr-2"></i>
            Conecta tus perfiles de redes sociales para que la gente pueda seguirte en diferentes plataformas
        </p>
    </div>
    
    <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-kraftdo-navy mb-2">
                    <i class="fab fa-linkedin text-blue-600 mr-1"></i> LinkedIn:
                </label>
                <input type="url" 
                       name="data[social_links][linkedin]" 
                       value="{{ old('data.social_links.linkedin', $content->data['social_links']['linkedin'] ?? '') }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-kraftdo-green focus:border-kraftdo-green transition-all duration-200 bg-white/80 backdrop-blur-sm"
                       placeholder="https://linkedin.com/in/tu-perfil">
                @error('data.social_links.linkedin')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-kraftdo-navy mb-2">
                    <i class="fab fa-twitter text-blue-400 mr-1"></i> Twitter:
                </label>
                <input type="url" 
                       name="data[social_links][twitter]" 
                       value="{{ old('data.social_links.twitter', $content->data['social_links']['twitter'] ?? '') }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-kraftdo-green focus:border-kraftdo-green transition-all duration-200 bg-white/80 backdrop-blur-sm"
                       placeholder="https://twitter.com/tu-usuario">
                @error('data.social_links.twitter')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-kraftdo-navy mb-2">
                    <i class="fab fa-instagram text-pink-500 mr-1"></i> Instagram:
                </label>
                <input type="url" 
                       name="data[social_links][instagram]" 
                       value="{{ old('data.social_links.instagram', $content->data['social_links']['instagram'] ?? '') }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-kraftdo-green focus:border-kraftdo-green transition-all duration-200 bg-white/80 backdrop-blur-sm"
                       placeholder="https://instagram.com/tu-usuario">
                @error('data.social_links.instagram')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-kraftdo-navy mb-2">
                    <i class="fab fa-facebook text-blue-600 mr-1"></i> Facebook:
                </label>
                <input type="url" 
                       name="data[social_links][facebook]" 
                       value="{{ old('data.social_links.facebook', $content->data['social_links']['facebook'] ?? '') }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-kraftdo-green focus:border-kraftdo-green transition-all duration-200 bg-white/80 backdrop-blur-sm"
                       placeholder="https://facebook.com/tu-usuario">
                @error('data.social_links.facebook')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-kraftdo-navy mb-2">
                    <i class="fab fa-youtube text-red-500 mr-1"></i> YouTube:
                </label>
                <input type="url" 
                       name="data[social_links][youtube]" 
                       value="{{ old('data.social_links.youtube', $content->data['social_links']['youtube'] ?? '') }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-kraftdo-green focus:border-kraftdo-green transition-all duration-200 bg-white/80 backdrop-blur-sm"
                       placeholder="https://youtube.com/c/tu-canal">
                @error('data.social_links.youtube')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-kraftdo-navy mb-2">
                    <i class="fab fa-tiktok text-black mr-1"></i> TikTok:
                </label>
                <input type="url" 
                       name="data[social_links][tiktok]" 
                       value="{{ old('data.social_links.tiktok', $content->data['social_links']['tiktok'] ?? '') }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-kraftdo-green focus:border-kraftdo-green transition-all duration-200 bg-white/80 backdrop-blur-sm"
                       placeholder="https://tiktok.com/@tu-usuario">
                @error('data.social_links.tiktok')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-kraftdo-navy mb-2">
                    <i class="fab fa-telegram text-blue-400 mr-1"></i> Telegram:
                </label>
                <input type="url" 
                       name="data[social_links][telegram]" 
                       value="{{ old('data.social_links.telegram', $content->data['social_links']['telegram'] ?? '') }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-kraftdo-green focus:border-kraftdo-green transition-all duration-200 bg-white/80 backdrop-blur-sm"
                       placeholder="https://t.me/tu-usuario">
                @error('data.social_links.telegram')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-kraftdo-navy mb-2">
                    <i class="fab fa-discord text-indigo-500 mr-1"></i> Discord:
                </label>
                <input type="url" 
                       name="data[social_links][discord]" 
                       value="{{ old('data.social_links.discord', $content->data['social_links']['discord'] ?? '') }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-kraftdo-green focus:border-kraftdo-green transition-all duration-200 bg-white/80 backdrop-blur-sm"
                       placeholder="https://discord.gg/tu-servidor">
                @error('data.social_links.discord')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-kraftdo-navy mb-2">
                    <i class="fab fa-snapchat text-yellow-400 mr-1"></i> Snapchat:
                </label>
                <input type="url" 
                       name="data[social_links][snapchat]" 
                       value="{{ old('data.social_links.snapchat', $content->data['social_links']['snapchat'] ?? '') }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-kraftdo-green focus:border-kraftdo-green transition-all duration-200 bg-white/80 backdrop-blur-sm"
                       placeholder="https://snapchat.com/add/tu-usuario">
                @error('data.social_links.snapchat')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-kraftdo-navy mb-2">
                    <i class="fab fa-threads text-kraftdo-navy mr-1"></i> Threads:
                </label>
                <input type="url" 
                       name="data[social_links][threads]" 
                       value="{{ old('data.social_links.threads', $content->data['social_links']['threads'] ?? '') }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-kraftdo-green focus:border-kraftdo-green transition-all duration-200 bg-white/80 backdrop-blur-sm"
                       placeholder="https://threads.net/@tu-usuario">
                @error('data.social_links.threads')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-kraftdo-navy mb-2">
                    <i class="fab fa-github text-gray-800 mr-1"></i> GitHub:
                </label>
                <input type="url" 
                       name="data[social_links][github]" 
                       value="{{ old('data.social_links.github', $content->data['social_links']['github'] ?? '') }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-kraftdo-green focus:border-kraftdo-green transition-all duration-200 bg-white/80 backdrop-blur-sm"
                       placeholder="https://github.com/tu-usuario">
                @error('data.social_links.github')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-kraftdo-navy mb-2">
                    <i class="fab fa-spotify text-green-400 mr-1"></i> Spotify:
                </label>
                <input type="url" 
                       name="data[social_links][spotify]" 
                       value="{{ old('data.social_links.spotify', $content->data['social_links']['spotify'] ?? '') }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-kraftdo-green focus:border-kraftdo-green transition-all duration-200 bg-white/80 backdrop-blur-sm"
                       placeholder="https://open.spotify.com/user/tu-usuario">
                @error('data.social_links.spotify')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
    
    <div class="mt-4 p-3 bg-kraftdo-lime/10 border border-kraftdo-green/30 rounded-lg">
        <p class="text-kraftdo-navy text-sm">
            <i class="fas fa-info-circle text-kraftdo-blue mr-2"></i>
            Agrega tus redes sociales para que las personas puedan seguirte y conectar contigo en diferentes plataformas. WhatsApp se configura en Información de Contacto.
        </p>
    </div>
</div>