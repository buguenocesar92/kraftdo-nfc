# Sistema de Permisos para Tokens NFC

## Permisos Implementados

### Permisos de Tokens Personales
- `view_own_tokens` - Ver listado de tokens propios
- `configure_own_tokens` - Configurar contenido de tokens propios
- `manage_own_token_content` - Gestionar contenido multimedia de tokens

## Roles y Permisos

### Super Admin
- Acceso total a todas las funciones

### Admin
- Gestión completa de tokens de todos los usuarios
- Gestión de usuarios y roles

### Editor
- Acceso a sus propios tokens
- Configuración de contenido

### Content Manager
- Gestión de contenido en general
- Gestión de tokens

### NFC User (Rol por defecto)
- `access_admin_panel` - Acceder al panel administrativo
- `view_own_tokens` - Ver sus propios tokens
- `configure_own_tokens` - Configurar sus tokens
- `manage_own_token_content` - Gestionar contenido de sus tokens

### Viewer
- Solo lectura (no incluye tokens personales por defecto)

## Protección de Rutas

### Páginas Protegidas

1. **MyTokensList** (`/admin/my-tokens`)
   - Requiere: `view_own_tokens`
   - Verifica: Usuario autenticado
   - Filtra: Solo tokens del usuario logueado

2. **MyTokens** (`/admin/my-tokens/{tokenId}/configure`)
   - Requiere: `configure_own_tokens`
   - Verifica: Propiedad del token
   - Valida: Token tipo GIFT

### Validaciones de Seguridad

1. **Propiedad del Token**
   ```php
   if ($token->user_id !== auth()->id()) {
       abort(403, 'No tienes permisos para acceder a este token.');
   }
   ```

2. **Permisos de Usuario**
   ```php
   if (!auth()->user()->can('configure_own_tokens')) {
       abort(403, 'No tienes permisos para configurar tokens.');
   }
   ```

3. **Tipo de Token**
   ```php
   ->visible(fn (NfcToken $record) => 
       $record->content_type === 'GIFT' && 
       auth()->user()->can('configure_own_tokens') &&
       $record->user_id === auth()->id()
   )
   ```

## Aplicar Permisos

Para aplicar estos permisos en el sistema:

```bash
# Ejecutar seeder de roles y permisos
php artisan db:seed RolesAndPermissionsSeeder

# O recrear toda la base de datos
php artisan migrate:fresh --seed
```

## Asignar Rol a Usuario

```php
// Asignar rol NFC a usuario nuevo (ejemplo)
$user->assignRole('NFC');

// Verificar permisos
$user->can('view_own_tokens'); // true
$user->can('configure_own_tokens'); // true
```

## Notas Importantes

1. **Solo tokens propios**: Los usuarios solo pueden ver y configurar tokens que les pertenecen
2. **Solo tokens GIFT**: La configuración avanzada solo está disponible para tokens de tipo regalo
3. **Validación múltiple**: Se valida tanto a nivel de página como a nivel de acción
4. **Middleware opcional**: Se incluye TokenOwnershipMiddleware para validación adicional si se requiere

## Extensión Futura

Para agregar más permisos específicos:

1. Agregar el permiso al array en `RolesAndPermissionsSeeder`
2. Asignarlo a los roles correspondientes
3. Usar `auth()->user()->can('nuevo_permiso')` en las páginas
4. Ejecutar el seeder para aplicar cambios