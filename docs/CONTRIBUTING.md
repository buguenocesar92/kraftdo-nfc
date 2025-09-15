# 🤝 Guía de Contribución - KraftDo NFC

¡Gracias por tu interés en contribuir a KraftDo NFC! Esta guía te ayudará a contribuir de manera efectiva.

## 📋 **Tabla de Contenidos**

- [🚀 Inicio Rápido](#-inicio-rápido)
- [🏗️ Configuración del Entorno](#️-configuración-del-entorno)
- [📝 Estándares de Código](#-estándares-de-código)
- [🧪 Testing](#-testing)
- [🔄 Workflow de Git](#-workflow-de-git)
- [📦 Pull Requests](#-pull-requests)
- [🐛 Reportar Bugs](#-reportar-bugs)
- [✨ Solicitar Features](#-solicitar-features)

## 🚀 **Inicio Rápido**

1. **Fork** el repositorio
2. **Clona** tu fork localmente
3. **Configura** el entorno de desarrollo
4. **Crea** una branch para tu feature/fix
5. **Desarrolla** tu cambio
6. **Prueba** tu cambio
7. **Envía** un pull request

## 🏗️ **Configuración del Entorno**

### **Requisitos**
- PHP 8.3+
- Node.js 20+
- Docker y Docker Compose
- Git

### **Setup Local**

```bash
# 1. Clonar tu fork
git clone https://github.com/tu-usuario/kraftdo-nfc.git
cd kraftdo-nfc

# 2. Instalar con un comando
make install

# 3. O manualmente:
cp .env.example .env
docker compose -f docker-compose.dev.yml up -d --build
make migrate
```

### **Verificar Instalación**

```bash
# Verificar que todo funciona
make health
make test-full
```

## 📝 **Estándares de Código**

### **PHP/Laravel**

Seguimos las convenciones de Laravel y PSR-12:

```php
<?php
// ✅ Bueno
class NfcTokenController extends Controller
{
    public function store(StoreNfcTokenRequest $request): JsonResponse
    {
        $token = NfcToken::create($request->validated());
        
        return response()->json([
            'message' => 'Token created successfully',
            'data' => $token
        ]);
    }
}

// ❌ Malo
class nfcTokenController {
    public function store($request) {
        $token=NfcToken::create($request->all());
        return $token;
    }
}
```

### **Convenciones de Naming**

- **Clases**: `PascalCase` (ej: `NfcTokenService`)
- **Métodos/Variables**: `camelCase` (ej: `calculateViewCount`)
- **Rutas**: `kebab-case` (ej: `/nfc-tokens`)
- **Base de datos**: `snake_case` (ej: `nfc_tokens`)

### **Comentarios y Documentación**

```php
/**
 * Calculate the total views for a specific NFC token
 *
 * @param NfcToken $token The NFC token to analyze
 * @param Carbon|null $startDate Optional start date for filtering
 * @param Carbon|null $endDate Optional end date for filtering
 * @return int Total number of views
 * 
 * @throws InvalidArgumentException When date range is invalid
 */
public function calculateViews(NfcToken $token, ?Carbon $startDate = null, ?Carbon $endDate = null): int
{
    // Implementation here
}
```

### **Frontend (Livewire/Alpine.js)**

```php
// ✅ Bueno - Componente Livewire
class TokenAnalytics extends Component
{
    public NfcToken $token;
    public string $dateRange = '7d';
    
    #[Computed]
    public function analytics(): array
    {
        return $this->token->calculateAnalytics($this->dateRange);
    }
}
```

## 🧪 **Testing**

### **Ejecutar Tests**

```bash
# Todos los tests
make test-full

# Solo unit tests
vendor/bin/pest tests/Unit/

# Tests con coverage
vendor/bin/pest --coverage --min=85
```

### **Escribir Tests**

#### **Unit Test Example:**

```php
<?php

uses(TestCase::class, RefreshDatabase::class);

describe('NfcToken', function () {
    it('can calculate view metrics correctly', function () {
        $token = NfcToken::factory()->create();
        
        // Create some analytics data
        NfcAnalytic::factory()->count(5)->create([
            'nfc_token_id' => $token->id,
            'created_at' => now()->subDays(2)
        ]);
        
        $metrics = $token->calculateViewMetrics();
        
        expect($metrics['total_views'])->toBe(5);
        expect($metrics['daily_average'])->toBeGreaterThan(0);
    });
});
```

#### **Feature Test Example:**

```php
<?php

uses(TestCase::class, RefreshDatabase::class);

it('can create nfc token via api', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->postJson('/api/nfc-tokens', [
        'name' => 'Test Token',
        'content_type' => 'profile',
        'content_data' => ['name' => 'John Doe']
    ]);
    
    $response->assertStatus(201);
    expect(NfcToken::count())->toBe(1);
});
```

### **Coverage Requirements**

- **Unit tests**: Mínimo 85%
- **Feature tests**: Cubrir casos críticos
- **Nuevas features**: 90%+ de coverage

## 🔄 **Workflow de Git**

### **Branches**

- `main`: Código de producción
- `develop`: Rama de desarrollo
- `feature/descripcion`: Nuevas características
- `fix/descripcion`: Bug fixes
- `hotfix/descripcion`: Fixes críticos para producción

### **Naming de Branches**

```bash
# ✅ Buenos nombres
feature/nfc-analytics-dashboard
fix/token-creation-validation
hotfix/security-vulnerability-auth

# ❌ Malos nombres
feature/stuff
fix/bug
new-feature
```

### **Commit Messages**

Usamos [Conventional Commits](https://www.conventionalcommits.org/):

```bash
# ✅ Formato correcto
feat(nfc): add analytics dashboard for token views
fix(auth): resolve login redirect loop
docs: update deployment guide
test: add unit tests for NfcToken model

# ❌ Formato incorrecto
fix stuff
updated files
changes
```

### **Tipos de Commits**

- `feat`: Nueva característica
- `fix`: Bug fix
- `docs`: Solo documentación
- `style`: Formato de código (no lógica)
- `refactor`: Refactoring sin cambios de funcionalidad
- `test`: Agregar o corregir tests
- `chore`: Cambios de build, configuración, etc.

## 📦 **Pull Requests**

### **Antes de Enviar**

```bash
# 1. Asegurar que los tests pasan
make test-full

# 2. Verificar code style
vendor/bin/php-cs-fixer fix --dry-run

# 3. Verificar que no hay conflictos
git pull origin develop
git rebase develop
```

### **Template de PR**

Usa el [template de PR](.github/PULL_REQUEST_TEMPLATE.md) que incluye:

- ✅ Descripción clara del cambio
- ✅ Tipo de cambio (bug fix, feature, etc.)
- ✅ Testing realizado
- ✅ Screenshots si aplica
- ✅ Checklist completo

### **Review Process**

1. **Automated checks** deben pasar
2. **Code review** por al menos un maintainer
3. **Testing** en staging si es necesario
4. **Merge** después de aprobación

## 🐛 **Reportar Bugs**

Usa el [template de bug report](.github/ISSUE_TEMPLATE/bug_report.yml):

### **Información Necesaria**

- ✅ **Descripción clara** del problema
- ✅ **Pasos para reproducir**
- ✅ **Comportamiento esperado vs actual**
- ✅ **Entorno** (desarrollo, staging, producción)
- ✅ **Screenshots/logs** si aplica
- ✅ **Severidad** del bug

## ✨ **Solicitar Features**

Usa el [template de feature request](.github/ISSUE_TEMPLATE/feature_request.yml):

### **Estructura de Request**

- ✅ **Problema que resuelve**
- ✅ **Solución propuesta**
- ✅ **Casos de uso específicos**
- ✅ **Prioridad estimada**
- ✅ **Mockups/referencias** si las tienes

## 🎯 **Áreas de Contribución**

### **🏷️ NFC Token Management**
- Mejoras en la gestión de tokens
- Nuevos tipos de contenido
- Validaciones adicionales

### **📊 Analytics & Reporting**
- Nuevas métricas
- Dashboards adicionales
- Exportación de datos

### **🎨 UI/UX**
- Mejoras en la interfaz
- Responsividad
- Accesibilidad

### **⚡ Performance**
- Optimizaciones de queries
- Mejoras en caching
- Reducción de tiempo de carga

### **🔒 Security**
- Mejoras en autenticación
- Validaciones adicionales
- Auditoría de seguridad

### **📚 Documentation**
- Mejoras en README
- Documentación de API
- Guías de usuario

### **🧪 Testing**
- Aumentar coverage
- Tests de integración
- Tests de performance

## 🏆 **Reconocimientos**

Los contribuidores aparecen en:

- 📝 **CONTRIBUTORS.md** (automático)
- 🎉 **Release notes** para contribuciones significativas
- 💫 **GitHub profile** como contributor

## 📞 **¿Necesitas Ayuda?**

- 💬 **GitHub Discussions**: Para preguntas generales
- 🐛 **GitHub Issues**: Para bugs específicos
- 📧 **Email**: dev@kraftdo.cl para consultas privadas

## 📄 **Licencia y Propiedad Intelectual**

Al contribuir a este proyecto, reconoces y aceptas que:

1. **Propiedad**: Este es un proyecto propietario de **KraftDo SpA**
2. **Confidencialidad**: Todo el código es confidencial y propietario
3. **Derechos**: Todas las contribuciones pasan a ser propiedad de KraftDo SpA
4. **Autorización**: Solo empleados, contratistas y socios autorizados pueden contribuir
5. **NDA**: Es posible que se requiera un acuerdo de confidencialidad (NDA)

Para más información sobre licencias y permisos, contacta: legal@kraftdo.cl

---

**🚀 ¡Gracias por contribuir a KraftDo NFC! Tu ayuda hace que esta plataforma sea mejor para todos.**

## ⚡ **Quick Reference**

```bash
# Setup rápido
make install

# Development
make dev

# Testing
make test-full

# Code style  
vendor/bin/php-cs-fixer fix

# Deployment
make deploy-prod

# Monitoreo
make health
```