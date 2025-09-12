<?php
/**
 * Health check endpoint para Docker
 * Verifica que la aplicación Laravel esté funcionando correctamente
 */

header('Content-Type: application/json');
http_response_code(200);

$health = [
    'status' => 'healthy',
    'timestamp' => date('c'),
    'environment' => $_ENV['APP_ENV'] ?? 'unknown',
    'checks' => []
];

// Verificar que Laravel puede cargar
try {
    if (file_exists('/var/www/html/bootstrap/app.php')) {
        $health['checks']['laravel'] = 'ok';
    } else {
        $health['checks']['laravel'] = 'missing bootstrap';
        $health['status'] = 'unhealthy';
        http_response_code(503);
    }
} catch (Exception $e) {
    $health['checks']['laravel'] = 'error: ' . $e->getMessage();
    $health['status'] = 'unhealthy';
    http_response_code(503);
}

// Verificar escritura en storage
try {
    $testFile = '/var/www/html/storage/logs/healthcheck.tmp';
    if (file_put_contents($testFile, time()) !== false) {
        $health['checks']['storage_write'] = 'ok';
        @unlink($testFile);
    } else {
        $health['checks']['storage_write'] = 'failed';
        $health['status'] = 'unhealthy';
        http_response_code(503);
    }
} catch (Exception $e) {
    $health['checks']['storage_write'] = 'error: ' . $e->getMessage();
    $health['status'] = 'unhealthy';
    http_response_code(503);
}

// Verificar conexión de base de datos (si está configurada)
if (!empty($_ENV['DB_HOST']) && $_ENV['DB_HOST'] !== 'localhost') {
    try {
        $pdo = new PDO(
            "mysql:host={$_ENV['DB_HOST']};port=" . ($_ENV['DB_PORT'] ?? 3306),
            $_ENV['DB_USERNAME'] ?? '',
            $_ENV['DB_PASSWORD'] ?? '',
            [PDO::ATTR_TIMEOUT => 5]
        );
        $health['checks']['database'] = 'ok';
    } catch (Exception $e) {
        $health['checks']['database'] = 'error: connection failed';
        $health['status'] = 'unhealthy';
        http_response_code(503);
    }
}

// Verificar conexión a Redis (si está configurada)
if (($_ENV['CACHE_DRIVER'] ?? '') === 'redis' || ($_ENV['SESSION_DRIVER'] ?? '') === 'redis') {
    try {
        $redis = new Redis();
        $redis->connect($_ENV['REDIS_HOST'] ?? 'localhost', $_ENV['REDIS_PORT'] ?? 6379, 5);
        if (!empty($_ENV['REDIS_PASSWORD'])) {
            $redis->auth($_ENV['REDIS_PASSWORD']);
        }
        $redis->ping();
        $health['checks']['redis'] = 'ok';
        $redis->close();
    } catch (Exception $e) {
        $health['checks']['redis'] = 'error: connection failed';
        $health['status'] = 'unhealthy';
        http_response_code(503);
    }
}

// Verificar uso de memoria
$memoryUsage = memory_get_usage(true);
$memoryLimit = ini_get('memory_limit');
if ($memoryLimit !== '-1') {
    $memoryLimitBytes = (int)$memoryLimit * 1024 * 1024;
    if ($memoryUsage > ($memoryLimitBytes * 0.9)) {
        $health['checks']['memory'] = 'warning: high usage';
        $health['status'] = 'degraded';
    } else {
        $health['checks']['memory'] = 'ok';
    }
} else {
    $health['checks']['memory'] = 'ok';
}

$health['memory_usage'] = round($memoryUsage / 1024 / 1024, 2) . 'MB';
$health['php_version'] = PHP_VERSION;

echo json_encode($health, JSON_PRETTY_PRINT);