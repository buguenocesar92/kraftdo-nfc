<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $contentGift?->recipient_name ? $contentGift->recipient_name . ' - Regalo NFC' : 'Regalo NFC' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-shadow {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .animate-fade-in {
            animation: fadeIn 0.8s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="h-full gradient-bg">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full space-y-8">
            <!-- Header -->
            <div class="text-center animate-fade-in">
                <h1 class="text-4xl font-bold text-white mb-2">🎁 Regalo Especial</h1>
                <p class="text-xl text-indigo-100">¡Tienes un regalo personalizado!</p>
            </div>

            <!-- Main Card -->
            <div class="bg-white rounded-2xl card-shadow p-8 animate-fade-in" style="animation-delay: 0.2s;">
                @if($contentGift)
                    <!-- Recipient Header -->
                    @if($contentGift->recipient_name)
                        <div class="text-center mb-8">
                            <h2 class="text-3xl font-bold text-gray-800 mb-2">
                                Para: {{ $contentGift->recipient_name }}
                            </h2>
                            @if($contentGift->sender_name)
                                <p class="text-lg text-gray-600">
                                    De: {{ $contentGift->sender_name }}
                                </p>
                            @endif
                        </div>
                    @endif

                    <!-- Personal Message -->
                    @if($contentGift->message)
                        <div class="bg-gradient-to-r from-pink-50 to-purple-50 rounded-xl p-6 mb-8">
                            <h3 class="text-xl font-semibold text-gray-800 mb-3">💌 Mensaje Personal</h3>
                            <p class="text-gray-700 leading-relaxed text-lg">{{ $contentGift->message }}</p>
                        </div>
                    @endif
                @endif

                <!-- Multimedia Content -->
                @if($contentMultimedia)
                    <div class="space-y-6">
                        <!-- Video Content -->
                        @if($contentMultimedia->video_url || $contentMultimedia->video_file)
                            <div class="bg-gray-50 rounded-xl p-6">
                                <h3 class="text-xl font-semibold text-gray-800 mb-4">🎬 Video</h3>
                                <div class="aspect-video rounded-lg overflow-hidden bg-black">
                                    @if($contentMultimedia->video_type === 'youtube' && $contentMultimedia->video_url)
                                        @php
                                            $videoId = '';
                                            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $contentMultimedia->video_url, $matches)) {
                                                $videoId = $matches[1];
                                            }
                                        @endphp
                                        @if($videoId)
                                            <iframe 
                                                class="w-full h-full" 
                                                src="https://www.youtube.com/embed/{{ $videoId }}?rel=0" 
                                                frameborder="0" 
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                allowfullscreen>
                                            </iframe>
                                        @endif
                                    @elseif($contentMultimedia->video_type === 'vimeo' && $contentMultimedia->video_url)
                                        @php
                                            $videoId = '';
                                            if (preg_match('/vimeo\.com\/(\d+)/', $contentMultimedia->video_url, $matches)) {
                                                $videoId = $matches[1];
                                            }
                                        @endphp
                                        @if($videoId)
                                            <iframe 
                                                class="w-full h-full" 
                                                src="https://player.vimeo.com/video/{{ $videoId }}" 
                                                frameborder="0" 
                                                allow="autoplay; fullscreen; picture-in-picture" 
                                                allowfullscreen>
                                            </iframe>
                                        @endif
                                    @elseif($contentMultimedia->video_type === 'file_upload' && $contentMultimedia->video_file)
                                        <video controls class="w-full h-full">
                                            <source src="{{ asset('storage/' . $contentMultimedia->video_file) }}" type="video/mp4">
                                            Tu navegador no soporta el elemento video.
                                        </video>
                                    @elseif($contentMultimedia->video_type === 'direct' && $contentMultimedia->video_url)
                                        <video controls class="w-full h-full">
                                            <source src="{{ $contentMultimedia->video_url }}" type="video/mp4">
                                            Tu navegador no soporta el elemento video.
                                        </video>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Audio Content -->
                        @if($contentMultimedia->audio_url || $contentMultimedia->audio_file)
                            <div class="bg-blue-50 rounded-xl p-6">
                                <h3 class="text-xl font-semibold text-gray-800 mb-4">🎵 Audio</h3>
                                <div class="bg-white rounded-lg p-4">
                                    @if($contentMultimedia->audio_type === 'file_upload' && $contentMultimedia->audio_file)
                                        <audio controls class="w-full">
                                            <source src="{{ asset('storage/' . $contentMultimedia->audio_file) }}" type="audio/mpeg">
                                            Tu navegador no soporta el elemento audio.
                                        </audio>
                                    @elseif($contentMultimedia->audio_type === 'direct' && $contentMultimedia->audio_url)
                                        <audio controls class="w-full">
                                            <source src="{{ $contentMultimedia->audio_url }}" type="audio/mpeg">
                                            Tu navegador no soporta el elemento audio.
                                        </audio>
                                    @elseif(in_array($contentMultimedia->audio_type, ['youtube_music', 'spotify', 'soundcloud']) && $contentMultimedia->audio_url)
                                        <div class="text-center">
                                            <a href="{{ $contentMultimedia->audio_url }}" 
                                               target="_blank" 
                                               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-blue-500 text-white font-semibold rounded-lg hover:from-green-600 hover:to-blue-600 transition-colors">
                                                🎧 Escuchar en {{ ucfirst($contentMultimedia->audio_type) }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Gallery Images -->
                        @if($galleryImages && count($galleryImages) > 0)
                            <div class="bg-yellow-50 rounded-xl p-6">
                                <h3 class="text-xl font-semibold text-gray-800 mb-4">📸 Galería de Imágenes</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($galleryImages as $image)
                                        <div class="relative group cursor-pointer" onclick="openImageModal('{{ $image->image_source }}', '{{ $image->alt_text }}')">
                                            <img src="{{ $image->image_source }}" 
                                                 alt="{{ $image->alt_text }}" 
                                                 class="w-full h-48 object-cover rounded-lg group-hover:opacity-80 transition-opacity">
                                            @if($image->caption)
                                                <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white p-2 rounded-b-lg">
                                                    <p class="text-sm">{{ $image->caption }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Footer -->
                <div class="mt-8 pt-6 border-t border-gray-200 text-center">
                    <p class="text-gray-500 text-sm">
                        Regalo creado con ❤️ usando KRAFTDO NFC
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4" onclick="closeImageModal()">
        <div class="max-w-4xl max-h-full relative">
            <img id="modalImage" src="" alt="" class="max-w-full max-h-full object-contain rounded-lg">
            <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white bg-black bg-opacity-50 rounded-full p-2 hover:bg-opacity-75">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    <script>
        function openImageModal(src, alt) {
            document.getElementById('modalImage').src = src;
            document.getElementById('modalImage').alt = alt;
            document.getElementById('imageModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>
</body>
</html>