{{-- Profile Social Networks Component --}}
@props([
    'content',
    'isDarkTheme' => false,
    'secondaryColor' => '#64748b',
    'primaryGradient' => '',
    'accentColor' => '#0ea5e9',
    'primaryColor' => '#1e40af',
    'cardStyle' => ''
])

@if(isset($content->data['social_networks']) || isset($content->data['social_links']))
    <!-- Redes Sociales -->
    <div class="mb-10">
        <div class="{{ $cardStyle }} rounded-3xl shadow-xl p-8 sm:p-10 border relative overflow-hidden" style="border-color: {{ $secondaryColor }};">
            
            <div class="absolute top-4 right-4 text-4xl opacity-10 {{ $isDarkTheme ? 'text-gray-400' : '' }}" style="{{ $isDarkTheme ? '' : 'color: ' . $secondaryColor . ';' }}">
                <i class="fas fa-network-wired"></i>
            </div>
            
            <div class="relative z-10">
                <h2 class="text-2xl sm:text-3xl font-bold mb-8 text-center {{ $isDarkTheme ? 'text-white' : 'text-transparent' }}" style="{{ $isDarkTheme ? '' : 'background: ' . $primaryGradient . '; background-clip: text; -webkit-background-clip: text;' }}">
                    🌐 {{ __('profile.connect_with_me') }}
                </h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                    @if(isset($content->data['social_networks']['linkedin']) || isset($content->data['social_links']['linkedin']))
                        @php
                            $linkedin = $content->data['social_networks']['linkedin'] ?? $content->data['social_links']['linkedin'] ?? '';
                        @endphp
                        <a href="{{ $linkedin }}" 
                           target="_blank"
                           class="group bg-gradient-to-r from-blue-600 to-blue-800 text-white py-4 px-6 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                            <i class="fab fa-linkedin mr-3 text-xl group-hover:animate-bounce"></i> 
                            <span class="font-semibold">LinkedIn</span>
                        </a>
                    @endif
                    
                    @if(isset($content->data['social_networks']['instagram']) || isset($content->data['social_links']['instagram']))
                        @php
                            $instagram = $content->data['social_networks']['instagram'] ?? $content->data['social_links']['instagram'] ?? '';
                        @endphp
                        <a href="{{ $instagram }}" 
                           target="_blank"
                           class="group bg-gradient-to-r from-purple-500 to-pink-500 text-white py-4 px-6 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                            <i class="fab fa-instagram mr-3 text-xl group-hover:animate-bounce"></i>
                            <span class="font-semibold">Instagram</span>
                        </a>
                    @endif
                    
                    @if(isset($content->data['social_networks']['twitter']) || isset($content->data['social_links']['twitter']))
                        @php
                            $twitter = $content->data['social_networks']['twitter'] ?? $content->data['social_links']['twitter'] ?? '';
                        @endphp
                        <a href="{{ $twitter }}" 
                           target="_blank"
                           class="group bg-gradient-to-r from-gray-800 to-black text-white py-4 px-6 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                            <i class="fab fa-x-twitter mr-3 text-xl group-hover:animate-bounce"></i> 
                            <span class="font-semibold">Twitter</span>
                        </a>
                    @endif
                    
                    @if(isset($content->data['social_networks']['facebook']) || isset($content->data['social_links']['facebook']))
                        @php
                            $facebook = $content->data['social_networks']['facebook'] ?? $content->data['social_links']['facebook'] ?? '';
                        @endphp
                        <a href="{{ $facebook }}" 
                           target="_blank"
                           class="group bg-gradient-to-r from-blue-500 to-blue-700 text-white py-4 px-6 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                            <i class="fab fa-facebook mr-3 text-xl group-hover:animate-bounce"></i> 
                            <span class="font-semibold">Facebook</span>
                        </a>
                    @endif
                    
                    @if(isset($content->data['social_networks']['youtube']) || isset($content->data['social_links']['youtube']))
                        @php
                            $youtube = $content->data['social_networks']['youtube'] ?? $content->data['social_links']['youtube'] ?? '';
                        @endphp
                        <a href="{{ $youtube }}" 
                           target="_blank"
                           class="group bg-gradient-to-r from-red-500 to-red-700 text-white py-4 px-6 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                            <i class="fab fa-youtube mr-3 text-xl group-hover:animate-bounce"></i> 
                            <span class="font-semibold">YouTube</span>
                        </a>
                    @endif
                    
                    @if(isset($content->data['social_networks']['tiktok']) || isset($content->data['social_links']['tiktok']))
                        @php
                            $tiktok = $content->data['social_networks']['tiktok'] ?? $content->data['social_links']['tiktok'] ?? '';
                        @endphp
                        <a href="{{ $tiktok }}" 
                           target="_blank"
                           class="group bg-gradient-to-r from-gray-900 to-black text-white py-4 px-6 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                            <i class="fab fa-tiktok mr-3 text-xl group-hover:animate-bounce"></i> 
                            <span class="font-semibold">TikTok</span>
                        </a>
                    @endif
                    
                    @if(isset($content->data['social_networks']['telegram']) || isset($content->data['social_links']['telegram']))
                        @php
                            $telegram = $content->data['social_networks']['telegram'] ?? $content->data['social_links']['telegram'] ?? '';
                        @endphp
                        <a href="{{ $telegram }}" 
                           target="_blank"
                           class="group bg-gradient-to-r from-blue-400 to-blue-600 text-white py-4 px-6 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                            <i class="fab fa-telegram mr-3 text-xl group-hover:animate-bounce"></i> 
                            <span class="font-semibold">Telegram</span>
                        </a>
                    @endif
                    
                    @if(isset($content->data['social_networks']['discord']) || isset($content->data['social_links']['discord']))
                        @php
                            $discord = $content->data['social_networks']['discord'] ?? $content->data['social_links']['discord'] ?? '';
                        @endphp
                        <a href="{{ $discord }}" 
                           target="_blank"
                           class="group bg-gradient-to-r from-indigo-500 to-purple-600 text-white py-4 px-6 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                            <i class="fab fa-discord mr-3 text-xl group-hover:animate-bounce"></i> 
                            <span class="font-semibold">Discord</span>
                        </a>
                    @endif
                    
                    @if(isset($content->data['social_networks']['snapchat']) || isset($content->data['social_links']['snapchat']))
                        @php
                            $snapchat = $content->data['social_networks']['snapchat'] ?? $content->data['social_links']['snapchat'] ?? '';
                        @endphp
                        <a href="{{ $snapchat }}" 
                           target="_blank"
                           class="group bg-gradient-to-r from-yellow-400 to-yellow-600 text-white py-4 px-6 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                            <i class="fab fa-snapchat mr-3 text-xl group-hover:animate-bounce"></i> 
                            <span class="font-semibold">Snapchat</span>
                        </a>
                    @endif
                    
                    @if(isset($content->data['social_networks']['threads']) || isset($content->data['social_links']['threads']))
                        @php
                            $threads = $content->data['social_networks']['threads'] ?? $content->data['social_links']['threads'] ?? '';
                        @endphp
                        <a href="{{ $threads }}" 
                           target="_blank"
                           class="group bg-gradient-to-r from-gray-700 to-gray-900 text-white py-4 px-6 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                            <i class="fab fa-threads mr-3 text-xl group-hover:animate-bounce"></i> 
                            <span class="font-semibold">Threads</span>
                        </a>
                    @endif
                    
                    @if(isset($content->data['social_networks']['github']) || isset($content->data['social_links']['github']))
                        @php
                            $github = $content->data['social_networks']['github'] ?? $content->data['social_links']['github'] ?? '';
                        @endphp
                        <a href="{{ $github }}" 
                           target="_blank"
                           class="group bg-gradient-to-r from-gray-800 to-gray-900 text-white py-4 px-6 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                            <i class="fab fa-github mr-3 text-xl group-hover:animate-bounce"></i> 
                            <span class="font-semibold">GitHub</span>
                        </a>
                    @endif
                    
                    @if(isset($content->data['social_networks']['spotify']) || isset($content->data['social_links']['spotify']))
                        @php
                            $spotify = $content->data['social_networks']['spotify'] ?? $content->data['social_links']['spotify'] ?? '';
                        @endphp
                        <a href="{{ $spotify }}" 
                           target="_blank"
                           class="group bg-gradient-to-r from-green-400 to-green-600 text-white py-4 px-6 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                            <i class="fab fa-spotify mr-3 text-xl group-hover:animate-bounce"></i> 
                            <span class="font-semibold">Spotify</span>
                        </a>
                    @endif
                    
                    @if(isset($content->data['social_networks']['website']) || isset($content->data['social_links']['website']))
                        @php
                            $website = $content->data['social_networks']['website'] ?? $content->data['social_links']['website'] ?? '';
                        @endphp
                        <a href="{{ $website }}" 
                           target="_blank"
                           class="group text-white py-4 px-6 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center" style="background: linear-gradient(135deg, {{ $accentColor }}, {{ $primaryColor }});">
                            <i class="fas fa-globe mr-3 text-xl group-hover:animate-bounce"></i>
                            <span class="font-semibold">Sitio Web</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif