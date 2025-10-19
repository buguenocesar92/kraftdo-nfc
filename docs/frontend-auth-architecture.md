# Frontend Authentication Architecture

## Overview

Este documento define la arquitectura de autenticación separada para usuarios finales vs administradores en el sistema NFC.

## Arquitectura Propuesta

### **Separación de Responsabilidades**

#### **Laravel Filament** - Administración Interna
- **Audiencia**: Staff interno (Admin, Editor, Content Manager, Super Admin)
- **Acceso**: `/admin/login`
- **Funcionalidad**: 
  - Gestión del sistema
  - Configuración avanzada
  - Analytics y reportes
  - Gestión de usuarios y roles

#### **Next.js Frontend** - Experiencia de Usuario Final
- **Audiencia**: Usuarios NFC y público general
- **Rutas de Auth**:
  ```
  /login          - Login usuarios NFC
  /register       - Registro usuarios NFC
  /forgot-password - Recuperación de contraseña
  ```
- **Post-Auth**:
  ```
  /onboarding/welcome      - Dashboard inicial
  /onboarding/create-token - Wizard creación de token
  /onboarding/token-type   - Selección tipo contenido
  /onboarding/content/*    - Formularios wizard por tipo
  /dashboard              - Panel usuario
  ```

## Implementación Técnica

### **API Authentication (Sanctum)**

```typescript
// Endpoints de autenticación
POST /api/register    - Registro de usuario
POST /api/login       - Autenticación
GET  /api/user        - Datos del usuario autenticado
POST /api/logout      - Cerrar sesión
POST /api/tokens      - Gestión de tokens NFC
```

### **Flujo de Autenticación**

1. **Registro/Login** → Next.js forms
2. **API Request** → Laravel backend
3. **Sanctum Token** → Respuesta de autenticación
4. **Token Storage** → localStorage/cookies en Next.js
5. **Authenticated Requests** → Header: `Authorization: Bearer {token}`

### **Estructura de Requests**

```typescript
// Registro
POST /api/register
{
  "name": "Usuario NFC",
  "email": "usuario@example.com", 
  "password": "password",
  "password_confirmation": "password"
}

// Login
POST /api/login
{
  "email": "usuario@example.com",
  "password": "password"
}

// Respuesta de autenticación
{
  "user": {
    "id": 1,
    "name": "Usuario NFC",
    "email": "usuario@example.com",
    "roles": ["NFC"]
  },
  "token": "1|AbCdEf..."
}
```

## Ventajas de esta Arquitectura

### **Experiencia de Usuario**
- **UX coherente**: Usuario nunca sale del frontend
- **Mobile-first**: Auth optimizado para dispositivos móviles
- **Performance**: SPA experience sin redirects cross-domain
- **Branding**: Control total sobre diseño y flujo

### **Técnicas**
- **Separación clara**: Admin vs User interfaces
- **Escalabilidad**: Frontend independiente del admin
- **Seguridad**: Tokens Sanctum para API access
- **Mantenibilidad**: Código separado por audiencia

### **Desarrollo**
- **Especialización**: Filament para admin, React para UX
- **Flexibilidad**: Cada frontend optimizado para su propósito
- **Testing**: Interfaces separadas = testing más enfocado

## Roles y Permisos

### **Filament (Admin Interface)**
- Super Admin: Acceso completo
- Admin: Gestión general
- Editor: Edición de contenido
- Content Manager: Gestión de contenido específico

### **Next.js (User Interface)**
- NFC Role: Acceso a onboarding y gestión de sus tokens
- Guest: Solo visualización pública de tokens

## Consideraciones de Implementación

### **Seguridad**
- Tokens Sanctum con expiración
- Validación de roles en cada request
- CORS configurado correctamente
- Rate limiting en endpoints de auth

### **Estado de Sesión**
- Token storage en localStorage
- Refresh automático de tokens
- Logout automático en token expiration
- Persistent login con remember token

### **Error Handling**
- Manejo de errores 401/403
- Redirects automáticos a login
- Mensajes de error user-friendly
- Fallbacks para conectividad

## Rutas y Navegación

### **Protección de Rutas**
```typescript
// Middleware de autenticación en Next.js
const withAuth = (WrappedComponent) => {
  return (props) => {
    const { user, loading } = useAuth();
    
    if (loading) return <LoadingSpinner />;
    if (!user) return <Navigate to="/login" />;
    
    return <WrappedComponent {...props} />;
  };
};
```

### **Flujo de Onboarding**
1. **Registro exitoso** → Redirect a `/onboarding/welcome`
2. **Login exitoso** → Redirect a `/dashboard` o última página visitada
3. **Usuario sin tokens** → Redirect a `/onboarding/create-token`
4. **Usuario con tokens** → Dashboard con lista de tokens

## Next Steps

1. **Implementar endpoints de auth en Laravel**
2. **Crear components de auth en Next.js**
3. **Configurar Sanctum para SPA**
4. **Implementar middleware de autenticación**
5. **Crear wizard de onboarding**
6. **Testing de flujos completos**

---

*Documento actualizado: 2025-10-19*
*Versión: 1.0*