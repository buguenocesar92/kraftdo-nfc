# Guía de Refactoring para Controladores

## Problemas Identificados en el Código Actual

### 1. **TokenController** - Principales Issues

#### 🚨 **Problemas Críticos:**
- **Método gigante** (`show()` con 375 líneas)
- **Responsabilidades múltiples** (lógica de negocio, presentación, validación)
- **Código duplicado** (manejo de JSON responses repetido 7 veces)
- **Hardcoded HTML** (líneas 251-350) mezclado con lógica de controlador
- **Debug code** en producción (líneas 19-21, 60-61, 152-158)
- **Validaciones inline** sin centralizar
- **Cache logic** mezclada con presentación

#### 🔧 **Refactoring Recomendado:**

```php
class TokenController extends Controller
{
    public function __construct(
        private TokenService $tokenService,
        private AnalyticsService $analyticsService
    ) {}

    public function show(Request $request, string $tokenId): JsonResponse|View
    {
        $tokenData = $this->tokenService->getTokenWithContent($tokenId);
        
        if (!$tokenData) {
            return $this->handleNotFound($request);
        }
        
        $this->analyticsService->recordAccess($tokenData);
        
        return $this->tokenService->renderResponse($request, $tokenData);
    }
    
    private function handleNotFound(Request $request): JsonResponse|Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'data' => null,
                'message' => 'Token no encontrado',
                'status' => 404
            ], 404);
        }
        
        abort(404, 'Token no encontrado');
    }
}
```

### 2. **Separación de Responsabilidades**

#### **Service Layer Pattern**

```php
// app/Services/TokenService.php
class TokenService
{
    public function getTokenWithContent(string $tokenId): ?array
    {
        // Cache logic aquí
    }
    
    public function renderResponse(Request $request, array $tokenData): JsonResponse|View
    {
        $renderer = $this->getRenderer($tokenData['token']->content_type);
        return $renderer->render($request, $tokenData);
    }
    
    private function getRenderer(string $contentType): ContentRendererInterface
    {
        return match($contentType) {
            'GIFT' => new GiftRenderer(),
            'PROFILE' => new ProfileRenderer(),
            'BUSINESS' => new BusinessRenderer(),
            'BUS_STOP' => new BusStopRenderer(),
            default => throw new UnsupportedContentTypeException()
        };
    }
}
```

#### **Content Renderers**

```php
// app/Services/Renderers/BusinessRenderer.php
class BusinessRenderer implements ContentRendererInterface
{
    public function render(Request $request, array $tokenData): JsonResponse|View
    {
        $data = $this->prepareData($tokenData);
        
        if ($request->expectsJson()) {
            return response()->json([
                'data' => $data,
                'message' => 'Token obtenido exitosamente',
                'status' => 200
            ]);
        }
        
        return view('token.business', $data);
    }
    
    private function prepareData(array $tokenData): array
    {
        // Lógica específica para business content
    }
}
```

### 3. **Resource Classes para APIs**

En lugar de arrays manuales, usar Laravel Resources:

```php
// app/Http/Resources/TokenResource.php
class TokenResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'token_id' => $this->token_id,
            'content_type' => $this->content_type,
            'is_active' => $this->is_active,
            'content' => $this->when($this->dynamicContent, function() {
                return $this->getContentResource();
            })
        ];
    }
    
    private function getContentResource()
    {
        return match($this->content_type) {
            'BUSINESS' => new BusinessContentResource($this->dynamicContent->content),
            'PROFILE' => new ProfileContentResource($this->dynamicContent->content),
            // ...
        };
    }
}
```

### 4. **Request Validation Classes**

```php
// app/Http/Requests/CreateBusinessContentRequest.php
class CreateBusinessContentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:500',
            'operating_hours' => 'nullable|array',
            'operating_hours.*.day' => 'required_with:operating_hours|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'operating_hours.*.hours' => 'required_with:operating_hours|string|max:50'
        ];
    }
    
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del negocio es obligatorio',
            'email.email' => 'El formato del email no es válido'
        ];
    }
}
```

### 5. **Enum para Content Types**

```php
// app/Enums/ContentType.php
enum ContentType: string
{
    case GIFT = 'GIFT';
    case PROFILE = 'PROFILE';
    case BUSINESS = 'BUSINESS';
    case TOURIST = 'TOURIST';
    case BUS_STOP = 'BUS_STOP';
    case BUSINESS_GROUP = 'BUSINESS_GROUP';
    
    public function getModelClass(): string
    {
        return match($this) {
            self::GIFT => ContentGift::class,
            self::PROFILE => ContentProfile::class,
            self::BUSINESS => ContentBusiness::class,
            self::TOURIST => ContentTourist::class,
            self::BUS_STOP => BusStop::class,
            self::BUSINESS_GROUP => ContentBusinessGroup::class,
        };
    }
    
    public function getViewName(): string
    {
        return match($this) {
            self::GIFT => 'token.gift',
            self::PROFILE => 'token.profile',
            self::BUSINESS => 'token.business',
            self::TOURIST => 'token.tourist',
            self::BUS_STOP => 'token.bus-stop',
            self::BUSINESS_GROUP => 'token.business-group',
        };
    }
}
```

## Plan de Refactoring por Fases

### **Fase 1: Limpieza Inmediata**
1. ✅ Eliminar debug code y logs innecesarios
2. ✅ Extraer hardcoded HTML a Blade templates
3. ✅ Crear método privado para response JSON consistency
4. ✅ Extraer validaciones a métodos separados

### **Fase 2: Service Layer**
1. ✅ Crear `TokenService` para lógica de negocio
2. ✅ Crear `AnalyticsService` para tracking
3. ✅ Mover cache logic a services
4. ✅ Implementar Content Renderers

### **Fase 3: API Resources**
1. ✅ Crear Resource classes para responses
2. ✅ Implementar Request classes para validación
3. ✅ Estandarizar error handling

### **Fase 4: Arquitectura Avanzada**
1. ✅ Implementar Content Type Enum
2. ✅ Factory pattern para content renderers
3. ✅ Repository pattern si es necesario
4. ✅ Event-driven analytics

## Estructura de Archivos Recomendada

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   │   ├── ContentController.php (refactored)
│   │   │   └── TokenController.php (API only)
│   │   └── TokenController.php (refactored)
│   ├── Requests/
│   │   ├── CreateBusinessContentRequest.php
│   │   ├── UpdateBusinessContentRequest.php
│   │   └── ...
│   └── Resources/
│       ├── TokenResource.php
│       ├── BusinessContentResource.php
│       └── ...
├── Services/
│   ├── TokenService.php
│   ├── AnalyticsService.php
│   └── Renderers/
│       ├── ContentRendererInterface.php
│       ├── BusinessRenderer.php
│       ├── ProfileRenderer.php
│       └── ...
├── Enums/
│   └── ContentType.php
└── ...
```

## Beneficios del Refactoring

### **Mantenibilidad**
- Código más legible y organizado
- Responsabilidades claras por clase
- Easier testing con dependencias inyectadas

### **Escalabilidad**
- Fácil agregar nuevos content types
- Renderer pattern permite customización
- Service layer reutilizable

### **Performance**
- Cache logic centralizado
- Eliminación de código duplicado
- Lazy loading optimizado

### **Developer Experience**
- IDE autocomplete mejor
- Type safety con enums
- Error handling consistente

## Comandos para Implementar

```bash
# Crear services
php artisan make:service TokenService
php artisan make:service AnalyticsService

# Crear renderers
php artisan make:class Services/Renderers/ContentRendererInterface
php artisan make:class Services/Renderers/BusinessRenderer

# Crear resources
php artisan make:resource TokenResource
php artisan make:resource BusinessContentResource

# Crear requests
php artisan make:request CreateBusinessContentRequest
php artisan make:request UpdateBusinessContentRequest

# Crear enum
php artisan make:enum ContentType
```

---

*Documento creado: 2025-10-19*
*Prioridad: Alta - Refactoring necesario para escalabilidad*