# 🚀 Cómo Agregar Nuevos Tipos de Contenido NFC

Esta guía te explica paso a paso cómo agregar un nuevo tipo de contenido al sistema NFC.

## 📋 Ejemplo: Agregar tipo "BUSINESS" (Tarjeta de Negocio)

### 1️⃣ **Agregar Constante en DynamicContent**
```php
// En app/Models/DynamicContent.php
public const TYPE_BUSINESS = 'BUSINESS';

// Agregar a getActiveTypes() cuando esté listo
```

### 2️⃣ **Crear Migración para Tabla Especializada**
```bash
php artisan make:migration create_content_business_table
```

```php
// En la migración
Schema::create('content_business', function (Blueprint $table) {
    $table->id();
    $table->foreignId('dynamic_content_id')->constrained('dynamic_content')->onDelete('cascade');
    $table->string('company_name');
    $table->string('position');
    $table->string('company_email')->nullable();
    $table->string('company_phone')->nullable();
    $table->text('company_address')->nullable();
    $table->string('website_url')->nullable();
    $table->text('services')->nullable();
    $table->string('working_hours')->nullable();
    $table->timestamps();
    
    $table->unique('dynamic_content_id');
});
```

### 3️⃣ **Crear Modelo**
```bash
php artisan make:model ContentBusiness
```

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class ContentBusiness extends Model
{
    protected $fillable = [
        'dynamic_content_id',
        'company_name',
        'position',
        'company_email',
        'company_phone',
        'company_address',
        'website_url',
        'services',
        'working_hours',
    ];

    public function dynamicContent(): BelongsTo
    {
        return $this->belongsTo(DynamicContent::class);
    }

    // Relaciones para multimedia y galería
    public function multimedia(): HasOneThrough
    {
        return $this->hasOneThrough(
            ContentMultimedia::class,
            DynamicContent::class,
            'id',
            'dynamic_content_id',
            'dynamic_content_id',
            'id'
        );
    }

    public function galleryImages(): HasManyThrough
    {
        return $this->hasManyThrough(
            ContentGalleryImage::class,
            ContentMultimedia::class,
            'dynamic_content_id',
            'content_multimedia_id',
            'dynamic_content_id',
            'id'
        );
    }
}
```

### 4️⃣ **Crear Directorio de Recursos Filament**
```bash
mkdir -p app/Filament/Resources/ContentBusiness/{Pages,Schemas,Tables}
```

### 5️⃣ **Crear ContentBusinessResource**
```php
<?php

namespace App\Filament\Resources\ContentBusiness;

use App\Models\ContentBusiness;
// ... imports necesarios

class ContentBusinessResource extends Resource
{
    protected static ?string $model = ContentBusiness::class;
    
    public static function getNavigationGroup(): ?string
    {
        return 'Contenido Especializado';
    }
    
    public static function getNavigationLabel(): string
    {
        return 'Tarjetas de Negocio';
    }

    public static function form(Schema $schema): Schema
    {
        return ContentBusinessForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContentBusinessTable::configure($table)
            ->modifyQueryUsing(fn ($query) => $query->with(['dynamicContent', 'multimedia', 'galleryImages']));
    }
}
```

### 6️⃣ **Crear Formulario**
```php
<?php

namespace App\Filament\Resources\ContentBusiness\Schemas;

use App\Filament\Components\GallerySection;
use App\Filament\Components\MultimediaSection;
use App\Models\DynamicContent;

class ContentBusinessForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Información de Negocio')->schema([
                Select::make('dynamic_content_id')
                    ->relationship('dynamicContent', 'title')
                    ->required(),
                TextInput::make('company_name')->label('Nombre de Empresa'),
                TextInput::make('position')->label('Cargo/Posición'),
                TextInput::make('company_email')->email(),
                // ... más campos
            ]),
            
            MultimediaSection::make(),
            GallerySection::make(),
        ]);
    }
}
```

### 7️⃣ **Crear Tabla**
```php
<?php

namespace App\Filament\Resources\ContentBusiness\Tables;

class ContentBusinessTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('dynamicContent.title'),
            TextColumn::make('company_name'),
            TextColumn::make('position'),
            // ... más columnas
        ]);
    }
}
```

### 8️⃣ **Crear Pages**
```php
// CreateContentBusiness.php
// EditContentBusiness.php  
// ListContentBusiness.php
```

### 9️⃣ **Actualizar DynamicContent**
```php
// Agregar relación en DynamicContent.php
public function business()
{
    return $this->hasOne(ContentBusiness::class);
}

// Mover de getFutureTypes() a getActiveTypes()
```

### 🔟 **Ejecutar Migración**
```bash
php artisan migrate
```

## 📝 **Checklist de Verificación**

- [ ] ✅ Constante agregada en DynamicContent
- [ ] ✅ Migración creada y ejecutada
- [ ] ✅ Modelo creado con relaciones
- [ ] ✅ Resource Filament creado
- [ ] ✅ Formulario con secciones multimedia y galería
- [ ] ✅ Tabla configurada con eager loading
- [ ] ✅ Pages creadas (Create, Edit, List)
- [ ] ✅ Relación agregada en DynamicContent
- [ ] ✅ Tipo movido a activos en getActiveTypes()
- [ ] ✅ Testear creación y edición

## 🎯 **Tipos Futuros Planificados**

Revisa `config/nfc_content_types.php` para ver la lista completa de tipos planificados con sus características específicas.

## 💡 **Tips**

1. **Usa los componentes existentes**: `MultimediaSection`, `GallerySection`, `SocialLinksSection`
2. **Sigue la convención de nombres**: `Content[Type]`, `Content[Type]Resource`
3. **Eager loading**: Siempre incluye relaciones en `modifyQueryUsing`
4. **Filtros**: Usa `modifyQueryUsing` en selectors para filtrar por tipo
5. **Testing**: Crea al menos un registro de prueba de cada tipo nuevo