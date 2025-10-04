<?php

namespace Database\Seeders;

use App\Models\ContentTourist;
use App\Models\DynamicContent;
use App\Models\NearbySpot;
use App\Models\NfcToken;
use App\Models\User;
use Illuminate\Database\Seeder;

class CristoMachaliSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usar el Super Administrator existente
        $user = User::find(1); // Super Administrator
        if (!$user) {
            // Fallback: buscar por email si no existe ID 1
            $user = User::where('email', 'admin@kraftdo-nfc.com')->first();
        }
        
        if (!$user) {
            throw new \Exception('Super Administrator no encontrado. Asegúrate de que existe un usuario con ID 1 o email admin@kraftdo-nfc.com');
        }

        // Crear el token NFC físico primero (UUID se genera automáticamente)
        $nfcToken = NfcToken::create([
            'user_id' => $user->id,
            'name' => 'Cristo de la Hacienda - Token Turístico',
            'content_type' => DynamicContent::TYPE_TOURIST,
            'customization_plan' => 'premium',
            'purchase_price' => 15000, // 15.000 CLP
            'purchase_currency' => 'CLP',
            'purchased_at' => now()->subDays(30),
            'purchase_notes' => 'Token para demostración del Cristo de Machalí',
            'is_active' => true,
        ]);

        // Crear el contenido dinámico principal
        $dynamicContent = DynamicContent::create([
            'content_id' => DynamicContent::generateUniqueContentId(DynamicContent::TYPE_TOURIST),
            'type' => DynamicContent::TYPE_TOURIST,
            'title' => 'Cristo de la Hacienda',
            'description' => 'Santuario de peregrinación envuelto en una fascinante leyenda de los años 1920. Un lugar sagrado donde la fe popular y la historia se encuentran en el corazón de Machalí.',
            'image_url' => null,
            'data' => [],
            'is_active' => true,
            'status' => 'published',
            'published_at' => now(),
            'user_id' => $user->id,
            'nfc_token_id' => $nfcToken->id, // Vincular con el token NFC
        ]);

        // Crear el contenido turístico específico
        $tourist = ContentTourist::create([
            'dynamic_content_id' => $dynamicContent->id,
            'location_name' => 'Cristo de la Hacienda',
            'place_type' => 'patrimonio',
            'location_address' => 'Camino La Hacienda, Machalí, Región de O\'Higgins, Chile',
            'history' => '<h2>Historia del Cristo de la Hacienda</h2>
            <p>El <strong>Cristo de la Hacienda</strong> es uno de los lugares de peregrinación más importantes de la región de O\'Higgins, envuelto en una fascinante leyenda que data de los años 1920-1930.</p>
            
            <h3>La Leyenda de Vicente Sanfuentes</h3>
            <p>La historia cuenta que <strong>Vicente Sanfuentes Moreno</strong>, dueño de la hacienda de Machalí, atravesaba una severa crisis económica. Según la leyenda local, Vicente hizo un pacto con el demonio para obtener dinero a cambio de su alma.</p>
            
            <p>Los lugareños relataban que "al comienzo del siglo XX, por ahí entre los años 20 y 30, se veía pasar por ese camino una carreta con caballos negros, jinetes negros que llevaban al diablo".</p>
            
            <h3>La Intervención Divina</h3>
            <p>Para proteger su hacienda y detener las visitas del demonio, Vicente (o según otras versiones, sus hijos) mandó a instalar una <strong>cruz con la imagen de Cristo crucificado</strong>. Esta cruz logró detener el paso de la carroza infernal, convirtiendo el lugar en un sitio sagrado.</p>
            
            <h3>Importancia Religiosa Actual</h3>
            <p>El Cristo de la Hacienda se ha convertido en un <strong>centro de peregrinación permanente</strong> durante todo el año. Devotos de diversas comunas llegan para agradecer milagros, pedir favores, y dejar ofrendas. Su popularidad fue tal que en 2005, Televisión Nacional de Chile realizó un capítulo especial en el programa "Pactos y Maleficios", recreando la historia en la misma hacienda Sanfuentes.</p>
            
            <p>Hoy en día, forma parte de la <strong>rica memoria histórica de Machalí</strong> y es considerado un foco del turismo religioso regional, manteniendo vivas las manifestaciones de fe popular.</p>',
            
            'latitude' => -34.183751,
            'longitude' => -70.6609645,
            'contact_phone' => '+56 2 2837 0000',
            'contact_email' => 'turismo@machali.cl',
            'website_url' => 'https://cristodelaacienda.blogspot.com',
            
            'gallery_images' => [],
            
            'opening_hours' => [
                'monday' => '06:00 - 18:00',
                'tuesday' => '06:00 - 18:00',
                'wednesday' => '06:00 - 18:00',
                'thursday' => '06:00 - 18:00',
                'friday' => '06:00 - 18:00',
                'saturday' => '06:00 - 19:00',
                'sunday' => '06:00 - 19:00',
            ],
            
            'pricing_info' => [
                'entrada' => 'Acceso libre y gratuito',
                'donaciones' => 'Bienvenidas para mantenimiento',
                'ofrendas' => 'Espacio disponible para ofrendas',
                'estacionamiento' => 'Gratuito en zona rural',
            ],
            
            'accessibility_info' => [
                'acceso_vehicular' => 'Por Camino La Hacienda',
                'estacionamiento' => 'Informal en zona rural',
                'sendero' => 'Acceso directo desde el camino',
                'dificultad' => 'Fácil',
                'wheelchair' => 'Accesible con asistencia',
                'baños' => 'No disponibles',
                'seguridad' => 'Zona rural, visitar en grupo recomendado',
            ],
            
            'services' => [
                'peregrinación' => 'Centro de peregrinación regional',
                'oración' => 'Espacio para oración y reflexión',
                'ofrendas' => 'Lugar para depositar ofrendas',
                'relatos_locales' => 'Lugareños comparten la leyenda',
                'turismo_religioso' => 'Parte del circuito religioso regional',
            ],
            
            'attractions' => [
                'leyenda_historica' => 'Famosa leyenda del pacto con el demonio',
                'patrimonio_religioso' => 'Santuario de devoción popular',
                'hacienda_historica' => 'Vestigios de la antigua hacienda Sanfuentes',
                'paisaje_rural' => 'Entorno rural tradicional chileno',
                'experiencia_espiritual' => 'Lugar de milagros y peticiones',
                'valor_cultural' => 'Parte de la identidad local de Machalí',
            ],
            
            'best_time_to_visit' => 'Todo el año. Fechas especiales: Semana Santa, festividades religiosas locales',
            
            'languages_spoken' => [
                'español' => 'Nativo',
                'inglés' => 'Básico (guías locales)',
            ],
        ]);

        // Sincronizar referencias en DynamicContent
        $dynamicContent->update(['tourist_id' => $tourist->id]);

        // Crear puntos cercanos de interés
        $nearbySpots = [
            [
                'name' => 'Restaurant El Mirador',
                'description' => 'Restaurante con comida típica chilena y vista al valle',
                'latitude' => -34.1845,
                'longitude' => -70.6590,
                'spot_type' => 'restaurante',
                'distance_km' => 0.8,
                'color' => '#FF6B35',
                'icon' => 'utensils',
                'additional_info' => [
                    'horario' => '12:00 - 22:00',
                    'especialidad' => 'Cordero al palo',
                    'precio_promedio' => '$15.000 - $25.000 CLP',
                    'reservas' => '+56 9 1234 5678',
                ],
            ],
            [
                'name' => 'Hotel Valle Andino',
                'description' => 'Hotel boutique con vista a la cordillera',
                'latitude' => -34.1820,
                'longitude' => -70.6580,
                'spot_type' => 'hotel',
                'distance_km' => 1.2,
                'color' => '#8B5CF6',
                'icon' => 'bed',
                'additional_info' => [
                    'categoría' => '4 estrellas',
                    'servicios' => 'Spa, piscina, restaurant',
                    'precio_desde' => '$80.000 CLP/noche',
                    'reservas' => 'www.valleandino.cl',
                ],
            ],
            [
                'name' => 'Parada de Colectivos',
                'description' => 'Transporte público hacia el centro de Machalí',
                'latitude' => -34.1855,
                'longitude' => -70.6620,
                'spot_type' => 'transporte',
                'distance_km' => 0.5,
                'color' => '#06B6D4',
                'icon' => 'bus',
                'additional_info' => [
                    'frecuencia' => 'Cada 20 minutos',
                    'horario' => '06:00 - 22:00',
                    'tarifa' => '$800 CLP',
                    'destino' => 'Plaza de Armas Machalí',
                ],
            ],
            [
                'name' => 'Sendero Las Águilas',
                'description' => 'Sendero de trekking con vista a la cordillera',
                'latitude' => -34.1825,
                'longitude' => -70.6595,
                'spot_type' => 'atraccion',
                'distance_km' => 1.5,
                'color' => '#F59E0B',
                'icon' => 'camera',
                'additional_info' => [
                    'dificultad' => 'Moderada a difícil',
                    'duración' => '3-4 horas ida y vuelta',
                    'mejor_época' => 'Marzo a Noviembre',
                    'equipamiento' => 'Zapatos de trekking recomendados',
                ],
            ],
            [
                'name' => 'Supermercado El Valle',
                'description' => 'Abastecimiento y productos locales',
                'latitude' => -34.1860,
                'longitude' => -70.6640,
                'spot_type' => 'comercio',
                'distance_km' => 0.9,
                'color' => '#10B981',
                'icon' => 'shopping-bag',
                'additional_info' => [
                    'horario' => '08:00 - 22:00',
                    'productos' => 'Abarrotes, productos locales, souvenirs',
                    'servicios' => 'ATM, farmacia',
                ],
            ],
            [
                'name' => 'Estación de Servicio Copec',
                'description' => 'Combustible y servicios básicos',
                'latitude' => -34.1870,
                'longitude' => -70.6630,
                'spot_type' => 'servicio',
                'distance_km' => 1.1,
                'color' => '#6B7280',
                'icon' => 'wrench',
                'additional_info' => [
                    'servicios' => 'Combustible, tienda, baños',
                    'horario' => '24 horas',
                    'métodos_pago' => 'Efectivo, tarjetas, app móvil',
                ],
            ],
            [
                'name' => 'Posta Rural Machalí',
                'description' => 'Centro de atención médica básica',
                'latitude' => -34.1840,
                'longitude' => -70.6625,
                'spot_type' => 'salud',
                'distance_km' => 1.0,
                'color' => '#DC2626',
                'icon' => 'plus-circle',
                'additional_info' => [
                    'servicios' => 'Atención primaria, urgencias menores',
                    'horario' => 'Lunes a Viernes 08:00-17:00',
                    'teléfono_urgencia' => '131',
                ],
            ],
            [
                'name' => 'Banco Estado ATM',
                'description' => 'Cajero automático',
                'latitude' => -34.1845,
                'longitude' => -70.6615,
                'spot_type' => 'banco',
                'distance_km' => 0.7,
                'color' => '#1F2937',
                'icon' => 'credit-card',
                'additional_info' => [
                    'disponibilidad' => '24 horas',
                    'servicios' => 'Retiro efectivo, consulta saldo',
                    'redes' => 'Redbanc, Maestro, Cirrus',
                ],
            ],
        ];

        foreach ($nearbySpots as $spotData) {
            NearbySpot::create(array_merge($spotData, [
                'content_tourist_id' => $tourist->id,
                'sort_order' => 0,
                'is_active' => true,
            ]));
        }

        $this->command->info('✅ Seeder del Cristo de la Hacienda ejecutado correctamente');
        $this->command->info("🏷️  Token NFC ID: {$nfcToken->token_id}");
        $this->command->info("🌐 URL por token: /nfc/{$nfcToken->token_id}");
        $this->command->info("🌐 URL por content: /c/{$dynamicContent->content_id}");
        $this->command->info("📊 Se crearon " . count($nearbySpots) . " puntos cercanos");
        $this->command->info("⛪ Sitio religioso: Cristo de la Hacienda, Machalí");
    }
}
