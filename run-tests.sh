#!/bin/bash

echo "🧪 EJECUTANDO SUITE COMPLETA DE TESTS NFC"
echo "=========================================="

# Configurar entorno de testing
export APP_ENV=testing
export DB_CONNECTION=sqlite
export DB_DATABASE=:memory:
export CACHE_DRIVER=array

echo "📋 Tests disponibles:"
echo "✅ Unit/Models/NfcTokenTest.php"
echo "✅ Unit/Models/NfcAnalyticTest.php" 
echo "✅ Unit/Services/NfcCacheServiceTest.php"
echo "✅ Unit/Observers/NfcTokenObserverTest.php"
echo "✅ Feature/Controllers/TokenControllerTest.php"
echo "✅ Feature/Commands/NfcCacheCommandsTest.php"

echo ""
echo "🚀 Iniciando tests..."

# Ejecutar tests uno por uno para ver resultados detallados
echo ""
echo "1️⃣ TESTING: NfcToken Model"
vendor/bin/pest tests/Unit/Models/NfcTokenTest.php --verbose

echo ""
echo "2️⃣ TESTING: NfcAnalytic Model"  
vendor/bin/pest tests/Unit/Models/NfcAnalyticTest.php --verbose

echo ""
echo "3️⃣ TESTING: NfcCacheService"
vendor/bin/pest tests/Unit/Services/NfcCacheServiceTest.php --verbose

echo ""
echo "4️⃣ TESTING: NfcTokenObserver"
vendor/bin/pest tests/Unit/Observers/NfcTokenObserverTest.php --verbose

echo ""
echo "5️⃣ TESTING: TokenController"
vendor/bin/pest tests/Feature/Controllers/TokenControllerTest.php --verbose

echo ""
echo "6️⃣ TESTING: Cache Commands"
vendor/bin/pest tests/Feature/Commands/NfcCacheCommandsTest.php --verbose

echo ""
echo "🎉 SUITE DE TESTS COMPLETADA"
echo "==============================="