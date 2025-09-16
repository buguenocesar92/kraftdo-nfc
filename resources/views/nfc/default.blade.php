<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $content->title }}</title>
    <meta name="description" content="{{ $content->description }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .content-container {
            background: linear-gradient(135deg, {{ $content->colors['primary'] }}20, {{ $content->colors['secondary'] }});
            min-height: 100vh;
        }
    </style>
</head>
<body class="content-container">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-lg max-w-2xl w-full p-8">
            
            <div class="text-center mb-6">
                <div class="text-6xl mb-4">{{ $content->icon }}</div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $content->title }}</h1>
                <p class="text-gray-600">{{ $content->type_name }}</p>
            </div>

            @if($content->image_url)
                <div class="mb-6">
                    <img src="{{ $content->image_url }}" 
                         alt="{{ $content->title }}"
                         class="w-full h-64 object-cover rounded-lg">
                </div>
            @endif

            <div class="mb-6">
                <p class="text-gray-700 text-lg leading-relaxed">{{ $content->description }}</p>
            </div>

            @if($content->data)
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="font-semibold text-gray-800 mb-3">Información Adicional</h3>
                    <pre class="text-sm text-gray-600 overflow-auto">{{ json_encode($content->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            @endif

            <div class="text-center mt-8 text-sm text-gray-500">
                @if(!isset($isPreview) || !$isPreview)
                    <p>Contenido NFC • ID: {{ $content->content_id }}</p>
                @else
                    <p class="text-blue-600 font-medium">📱 Vista Previa</p>
                @endif
            </div>
        </div>
    </div>
</body>
</html>