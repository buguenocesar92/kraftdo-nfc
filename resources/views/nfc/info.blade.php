<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información NFC - KraftDo</title>
    <meta name="description" content="Descubre cómo funciona nuestra tecnología NFC para contenido dinámico">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-gray-50">
    
    <!-- Header -->
    <div class="gradient-bg text-white">
        <div class="container mx-auto px-4 py-16">
            <div class="text-center">
                <div class="text-6xl mb-4">📱</div>
                <h1 class="text-4xl font-bold mb-4">Tecnología NFC</h1>
                <p class="text-xl opacity-90 max-w-2xl mx-auto">
                    Conecta el mundo físico con experiencias digitales únicas mediante chips NFC inteligentes
                </p>
            </div>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="container mx-auto px-4 py-12">
        
        <!-- ¿Qué es NFC? -->
        <div class="max-w-4xl mx-auto mb-12">
            <div class="bg-white rounded-xl shadow-lg p-8 card-hover">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">🔍 ¿Qué es NFC?</h2>
                <p class="text-gray-600 leading-relaxed mb-4">
                    NFC (Near Field Communication) es una tecnología de comunicación inalámbrica de corto alcance 
                    que permite el intercambio de datos entre dispositivos compatibles cuando se acercan a pocos centímetros.
                </p>
                <p class="text-gray-600 leading-relaxed">
                    Con nuestros chips NFC, puedes crear experiencias interactivas que se activan simplemente 
                    acercando un smartphone al chip. No necesitas aplicaciones especiales, ¡funciona directamente 
                    desde el navegador!
                </p>
            </div>
        </div>

        <!-- Tipos de Contenido -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                <div class="text-3xl mb-3">🎁</div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Regalos Personalizados</h3>
                <p class="text-gray-600 text-sm">
                    Crea mensajes especiales para aniversarios, cumpleaños, graduaciones y más.
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                <div class="text-3xl mb-3">🍽️</div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Menús de Restaurante</h3>
                <p class="text-gray-600 text-sm">
                    Menús digitales interactivos con fotos, precios y descripciones detalladas.
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                <div class="text-3xl mb-3">👤</div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Perfiles Profesionales</h3>
                <p class="text-gray-600 text-sm">
                    Tarjetas de presentación digitales con información de contacto y portafolio.
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                <div class="text-3xl mb-3">🗺️</div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Información Turística</h3>
                <p class="text-gray-600 text-sm">
                    Guías interactivas para lugares turísticos, museos y puntos de interés.
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                <div class="text-3xl mb-3">📅</div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Eventos</h3>
                <p class="text-gray-600 text-sm">
                    Información de eventos con programa, ubicación y detalles importantes.
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
                <div class="text-3xl mb-3">📦</div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Productos</h3>
                <p class="text-gray-600 text-sm">
                    Catálogos digitales con especificaciones, garantías y manuales.
                </p>
            </div>
        </div>

        <!-- Cómo Funciona -->
        <div class="max-w-4xl mx-auto mb-12">
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">🚀 ¿Cómo Funciona?</h2>
                
                <div class="grid md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl">1️⃣</span>
                        </div>
                        <h4 class="font-semibold text-gray-800 mb-2">Acerca tu teléfono</h4>
                        <p class="text-gray-600 text-sm">
                            Acerca tu smartphone compatible con NFC al chip
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl">2️⃣</span>
                        </div>
                        <h4 class="font-semibold text-gray-800 mb-2">Conexión automática</h4>
                        <p class="text-gray-600 text-sm">
                            El chip se activa y abre el contenido en tu navegador
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="bg-purple-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl">3️⃣</span>
                        </div>
                        <h4 class="font-semibold text-gray-800 mb-2">Disfruta el contenido</h4>
                        <p class="text-gray-600 text-sm">
                            Experimenta contenido interactivo personalizado
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Planes de Personalización -->
        <div class="max-w-5xl mx-auto mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-8 text-center">💎 Planes de Personalización</h2>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-gray-200">
                    <div class="text-center mb-4">
                        <div class="text-2xl mb-2">🥉</div>
                        <h3 class="text-lg font-semibold">Básico</h3>
                        <p class="text-gray-600 text-sm">Funciones esenciales</p>
                    </div>
                    <ul class="text-sm text-gray-600 space-y-2">
                        <li>✅ Mensaje de texto</li>
                        <li>✅ Música de fondo</li>
                        <li>✅ Receptor/Remitente</li>
                        <li>✅ Tipo de regalo</li>
                        <li>❌ Subida de imágenes</li>
                        <li>❌ Videos</li>
                        <li>❌ Galería</li>
                    </ul>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-blue-300">
                    <div class="text-center mb-4">
                        <div class="text-2xl mb-2">🥈</div>
                        <h3 class="text-lg font-semibold">Estándar</h3>
                        <p class="text-gray-600 text-sm">Más personalización</p>
                    </div>
                    <ul class="text-sm text-gray-600 space-y-2">
                        <li>✅ Todo del Básico</li>
                        <li>✅ Subida de imágenes</li>
                        <li>✅ Enlaces sociales</li>
                        <li>✅ Diseño personalizado</li>
                        <li>❌ Videos</li>
                        <li>❌ Galería avanzada</li>
                        <li>❌ Multimedia avanzado</li>
                    </ul>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-yellow-300">
                    <div class="text-center mb-4">
                        <div class="text-2xl mb-2">🥇</div>
                        <h3 class="text-lg font-semibold">Premium</h3>
                        <p class="text-gray-600 text-sm">Experiencia completa</p>
                    </div>
                    <ul class="text-sm text-gray-600 space-y-2">
                        <li>✅ Todo del Estándar</li>
                        <li>✅ Videos</li>
                        <li>✅ Galería de fotos</li>
                        <li>✅ Enlaces sociales</li>
                        <li>✅ Diseño avanzado</li>
                        <li>❌ Multimedia profesional</li>
                        <li>❌ Efectos especiales</li>
                    </ul>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-purple-300">
                    <div class="text-center mb-4">
                        <div class="text-2xl mb-2">💎</div>
                        <h3 class="text-lg font-semibold">Deluxe</h3>
                        <p class="text-gray-600 text-sm">Sin límites</p>
                    </div>
                    <ul class="text-sm text-gray-600 space-y-2">
                        <li>✅ Todo incluido</li>
                        <li>✅ Multimedia avanzado</li>
                        <li>✅ Efectos especiales</li>
                        <li>✅ Animaciones</li>
                        <li>✅ Interactividad total</li>
                        <li>✅ Soporte prioritario</li>
                        <li>✅ Estadísticas avanzadas</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center py-8 border-t border-gray-200">
            <p class="text-gray-600 mb-2">¿Tienes preguntas sobre nuestra tecnología NFC?</p>
            <a href="mailto:info@kraftdo.com" 
               class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                📧 Contáctanos
            </a>
        </div>
    </div>
</body>
</html>