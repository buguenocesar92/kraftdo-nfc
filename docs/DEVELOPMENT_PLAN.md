# Plan de Desarrollo - Frontend + Backend + Testing

## 🎯 **ESTRATEGIA GENERAL**

### **Metodología:**
- **TDD Approach**: Tests primero, después implementación
- **Backend + Frontend** en paralelo por feature
- **Pyramid Testing**: Unitarios → Integración → E2E
- **Validación continua** entre fases

### **Arquitectura Objetivo:**
- **Next.js Frontend**: Para usuarios finales NFC
- **Laravel Backend**: API + Admin panel (Filament para administradores)
- **Sanctum Auth**: Tokens para SPA authentication
- **Separación clara**: User experience vs Admin interface

---

## **📍 FASE 1: AUTH FOUNDATIONS**
*Objetivo: Base sólida de autenticación*

### 🏗️ **BACKEND TASKS:**
1. **Configurar Sanctum SPA**
   - CORS settings para Next.js
   - Session/Token configuration  
   - Config: `config/sanctum.php`
   
2. **API Endpoints Auth**
   ```php
   POST /api/register    // Registro de usuarios
   POST /api/login       // Autenticación con token
   POST /api/logout      // Cerrar sesión
   GET  /api/user        // Datos usuario autenticado
   ```

3. **Tests Backend**
   - `tests/Feature/Api/AuthTest.php`
   - `tests/Unit/Controllers/AuthControllerTest.php`

### 🎨 **FRONTEND TASKS:**
1. **Auth Components**
   ```typescript
   - components/auth/LoginForm.tsx
   - components/auth/RegisterForm.tsx  
   - components/auth/ForgotPasswordForm.tsx
   ```

2. **Auth Infrastructure**
   ```typescript
   - contexts/AuthContext.tsx
   - hooks/useAuth.ts
   - middleware/withAuth.ts
   ```

3. **Auth Routes**
   ```typescript
   - app/login/page.tsx
   - app/register/page.tsx
   - app/forgot-password/page.tsx
   ```

4. **Tests Frontend**
   - `__tests__/auth/LoginForm.test.tsx`
   - `__tests__/auth/AuthContext.test.tsx`
   - `__tests__/auth/useAuth.test.ts`

### 🧪 **TESTING STRATEGY:**
- **Unit**: Auth components + API endpoints individual
- **Integration**: API ↔ Frontend auth flow completo
- **E2E**: Login → Dashboard → Logout journey

---

## **📍 FASE 2: ONBOARDING FLOW**
*Objetivo: Primera experiencia usuario*

### 🎨 **FRONTEND LEAD:**
1. **Onboarding Layout**
   ```typescript
   - app/onboarding/layout.tsx
   - app/onboarding/welcome/page.tsx
   - components/onboarding/WelcomeWizard.tsx
   - components/onboarding/ProgressIndicator.tsx
   ```

2. **User State Detection**
   ```typescript
   - hooks/useFirstTimeUser.ts
   - components/dashboard/EmptyState.tsx
   - utils/userProgress.ts
   ```

### 🏗️ **BACKEND SUPPORT:**
1. **User Status API**
   ```php
   GET /api/user/status              // first_login, has_tokens, onboarding_complete
   PUT /api/user/onboarding-complete // Marcar onboarding terminado
   GET /api/user/progress            // Estado del progreso del usuario
   ```

2. **User Model Enhancement**
   - Migration: agregar campos onboarding
   - Model: métodos helper para estado

### 🧪 **TESTING FOCUS:**
- **Unit**: Onboarding components + user status logic
- **Integration**: User status detection y updates
- **E2E**: Register → Welcome → First Token flow

---

## **📍 FASE 3: TOKEN CREATION WIZARD**
*Objetivo: Crear primer token fácilmente*

### 🎨 **FRONTEND CORE:**
1. **Type Selection**
   ```typescript
   - app/onboarding/token-type/page.tsx
   - components/wizard/TypeSelector.tsx
   - components/wizard/ContentTypeCard.tsx
   ```

2. **Simplified Wizard**
   ```typescript
   - app/onboarding/create-token/page.tsx
   - components/wizard/TokenCreationWizard.tsx
   - components/wizard/steps/BasicInfo.tsx
   - components/wizard/steps/ContentSetup.tsx
   - components/wizard/steps/Preview.tsx
   ```

### 🏗️ **BACKEND OPTIMIZATION:**
1. **Simplified Token Creation**
   ```php
   POST /api/tokens/simple           // Minimal required fields
   GET /api/content-types            // Available types + descriptions
   POST /api/tokens/{id}/activate    // Publish token
   ```

2. **Content Type Service**
   - Service para metadatos de tipos
   - Validation rules por tipo
   - Template generation

### 🧪 **TESTING PRIORITY:**
- **Unit**: Wizard steps + validation + content types
- **Integration**: Token creation flow completo
- **E2E**: Complete first token creation por cada tipo

---

## **📍 FASE 4: USER DASHBOARD**
*Objetivo: Panel de gestión principal*

### 🎨 **FRONTEND EXPERIENCE:**
1. **Main Dashboard**
   ```typescript
   - app/dashboard/page.tsx
   - components/dashboard/TokensList.tsx
   - components/dashboard/QuickActions.tsx
   - components/dashboard/UserStats.tsx
   ```

2. **Token Management**
   ```typescript
   - components/tokens/TokenCard.tsx
   - components/tokens/TokenActions.tsx
   - components/tokens/TokenEditor.tsx
   ```

### 🏗️ **BACKEND ROBUSTNESS:**
1. **Full CRUD API**
   ```php
   GET    /api/user/tokens           // Lista tokens del usuario
   PUT    /api/tokens/{id}           // Actualizar token
   DELETE /api/tokens/{id}           // Eliminar token  
   POST   /api/tokens/{id}/duplicate // Duplicar token
   GET    /api/tokens/{id}/analytics // Métricas del token
   ```

2. **Authorization & Security**
   - Policies para ownership
   - Rate limiting
   - Input validation

### 🧪 **TESTING COMPLETENESS:**
- **Unit**: Dashboard components + authorization policies
- **Integration**: Token CRUD operations + ownership
- **E2E**: Multi-token management workflows

---

## **📍 FASE 5: ADVANCED FEATURES**
*Objetivo: Funcionalidades power user*

### 🎨 **FRONTEND POLISH:**
1. **Advanced Configuration**
   - Token settings avanzados
   - Bulk operations
   - Import/Export

2. **Analytics Dashboard** 
   - Métricas de uso
   - Charts y visualizaciones
   - Reports

3. **Sharing & Export**
   - QR code generation
   - Social sharing
   - Export formats

### 🏗️ **BACKEND PERFORMANCE:**
1. **Analytics API**
   - Real-time metrics
   - Aggregated reports
   - Data export

2. **Performance Optimization**
   - Redis caching enhancement
   - Query optimization
   - Background jobs

3. **Advanced Features**
   - Rate limiting per user
   - Webhook system
   - API versioning

---

## **🧪 TESTING STRATEGY POR FASE**

### **Testing Pyramid:**
```
       E2E Tests (5%)
    Integration Tests (15%)
   Unit Tests (80%)
```

### **Por Tecnología:**

#### **Backend (Laravel):**
- **Unit**: Models, Services, Controllers
- **Feature**: API endpoints, Authentication
- **Integration**: Database, External services

#### **Frontend (Next.js):**
- **Unit**: Components, Hooks, Utils
- **Integration**: API calls, State management
- **E2E**: User workflows con Playwright

### **Coverage Goals:**
- **Backend**: 85% mínimo
- **Frontend**: 80% mínimo
- **E2E**: Cubrir flows críticos

---

## **📁 ESTRUCTURA DE ARCHIVOS**

### **Backend:**
```
kraftdo-nfc/
├── app/Http/Controllers/Api/
│   ├── AuthController.php
│   ├── TokenController.php
│   └── UserController.php
├── tests/Feature/Api/
├── tests/Unit/Controllers/
└── docs/DEVELOPMENT_PLAN.md
```

### **Frontend:**
```
kraftdo-nfc-frontend/
├── src/app/
│   ├── (auth)/
│   ├── onboarding/
│   ├── dashboard/
│   └── login/
├── src/components/
│   ├── auth/
│   ├── onboarding/
│   └── wizard/
├── src/__tests__/
└── docs/DEVELOPMENT_PLAN.md
```

---

## **🚀 PRÓXIMOS PASOS**

### **FASE 1 - INICIO:**
1. ✅ **Documentar plan** (este archivo)
2. 🔄 **Configurar Sanctum SPA** en backend
3. 🔄 **Crear AuthController** con tests
4. 🔄 **Implementar componentes auth** en frontend
5. 🔄 **E2E auth flow**

### **Criterios de Completitud por Fase:**
- ✅ **Todos los tests pasan**
- ✅ **Coverage goals cumplidos**
- ✅ **E2E scenarios validados**
- ✅ **Documentation actualizada**

---

*Documento creado: 2025-10-25*
*Última actualización: 2025-10-25*