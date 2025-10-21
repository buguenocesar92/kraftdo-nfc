# n8n Integration Guide para KraftDo NFC

## ¿Qué problemas resuelve n8n en KraftDo?

### **Problema 1: "No sé qué está pasando con mis tokens NFC"**
**Sin n8n:** Tienes que entrar al admin panel para ver estadísticas
**Con n8n:** 
- Te llega un WhatsApp: "Tu token del restaurante tuvo 50 escaneos hoy"
- Email semanal: "Resumen: tus 10 tokens más populares esta semana"
- Slack alert: "¡Token de turismo en Valparaíso tuvo pico de actividad!"

### **Problema 2: "Tengo que actualizar manualmente en varios lugares"**
**Sin n8n:** Cliente actualiza su menú → tienes que:
1. Actualizarlo en KraftDo
2. Subirlo a Instagram
3. Enviarlo por email a clientes
4. Actualizarlo en Google My Business

**Con n8n:** Cliente actualiza una vez → automáticamente se actualiza en todos lados

### **Problema 3: "Pierdo oportunidades de negocio"**
**Sin n8n:** Nuevo cliente se registra → queda ahí sin seguimiento
**Con n8n:**
- Automáticamente le llega email de bienvenida
- Se agenda llamada de onboarding 
- Se crea lead en tu CRM
- Se notifica al equipo de ventas

## Casos de uso reales con valor inmediato

### **Para TI (restaurantes):**
```
Cliente escanea token del restaurante → n8n →
"¡Hola! Veo que estás en Restaurante X. 
Aquí tienes un 10% de descuento para tu próxima visita: CODIGO123"
```
**Valor:** Aumentas las ventas repeat del restaurante

### **Para TUS CLIENTES (dueños de restaurantes):**
```
Se agota un producto del menú → n8n →
- Email automático al dueño: "Se agotó el Lomo Saltado"
- WhatsApp a meseros: "Avisar que no hay Lomo Saltado"
- Auto-desactivar del menú digital
```
**Valor:** El restaurante no pierde ventas por productos agotados

### **Para TU NEGOCIO:**
```
Token no se escanea en 7 días → n8n →
- Email al cliente: "¿Todo bien con tu token? ¿Necesitas ayuda?"
- Alerta a tu equipo: "Cliente X puede estar teniendo problemas"
```
**Valor:** Reduces la pérdida de clientes (churn)

### **Para MARKETING:**
```
Token popular (>100 escaneos/día) → n8n →
- Crear post automático: "¡Mira este restaurante exitoso con KraftDo!"
- Contactar al dueño para caso de éxito
- Crear contenido para redes sociales
```
**Valor:** Marketing automático con casos reales

## Los 3 flujos que más dinero te van a generar

### **1. Retención de clientes** 💰
```
Cliente no usa token en 14 días → 
Email: "¿Necesitas ayuda con tu token NFC?"
Si no responde → WhatsApp del account manager
```

### **2. Upselling automático** 💰💰
```
Cliente con 1 token y >50 escaneos/semana →
Email: "¡Tu token es popular! ¿Quieres más tokens para otras mesas/productos?"
```

### **3. Referencias automáticas** 💰💰💰
```
Cliente feliz (token popular) →
Email: "¡Tu token va súper bien! ¿Conoces otros restaurantes que podrían usar KraftDo?"
+ Descuento por referido
```

## Implementación técnica

### **Arquitectura propuesta:**

```
┌─────────────────┐    ┌─────────────┐    ┌─────────────────┐
│   Laravel API   │────│     n8n     │────│  External APIs  │
│                 │    │   Engine    │    │                 │
│ ┌─────────────┐ │    │             │    │ ├─ Google       │
│ │  Webhooks   │ │    │ ┌─────────┐ │    │ ├─ Social Media │
│ │  Events     │ │────│ │Workflows│ │────│ ├─ CRM          │
│ │  Observers  │ │    │ │Rules    │ │    │ ├─ Email        │
│ └─────────────┘ │    │ │Triggers │ │    │ └─ Storage      │
└─────────────────┘    │ └─────────┘ │    └─────────────────┘
                       └─────────────┘
```

### **Setup Docker Compose:**

```yaml
# docker-compose.n8n.yml
services:
  n8n:
    image: n8nio/n8n
    ports:
      - "5678:5678"
    environment:
      - N8N_BASIC_AUTH_ACTIVE=true
      - N8N_BASIC_AUTH_USER=admin
      - N8N_BASIC_AUTH_PASSWORD=kraftdo123
      - WEBHOOK_URL=http://localhost:5678/
    volumes:
      - n8n_data:/home/node/.n8n
    networks:
      - kraftdo_network

  kraftdo-app:
    # tu app actual
    networks:
      - kraftdo_network

volumes:
  n8n_data:

networks:
  kraftdo_network:
    external: true
```

### **Implementación en Laravel - Webhooks**

#### **1. Webhook desde Observer:**
```php
// app/Observers/NfcAnalyticObserver.php
class NfcAnalyticObserver
{
    public function created(NfcAnalytic $analytic)
    {
        // Trigger n8n workflow
        Http::post('http://n8n:5678/webhook/token-scanned', [
            'token_id' => $analytic->nfc_token_id,
            'location' => $analytic->location,
            'timestamp' => $analytic->created_at,
            'content_type' => $analytic->nfcToken->content_type,
            'user_id' => $analytic->nfcToken->user_id
        ]);
    }
}
```

#### **2. Webhook para registro de usuarios:**
```php
// app/Observers/UserObserver.php
class UserObserver
{
    public function created(User $user)
    {
        Http::post('http://n8n:5678/webhook/user-registered', [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'registration_date' => $user->created_at
        ]);
    }
}
```

#### **3. Webhook para actualizaciones de contenido:**
```php
// app/Observers/ContentBusinessObserver.php
class ContentBusinessObserver
{
    public function updated(ContentBusiness $business)
    {
        Http::post('http://n8n:5678/webhook/content-updated', [
            'business_id' => $business->id,
            'business_name' => $business->business_name,
            'update_type' => 'business_content',
            'updated_fields' => $business->getDirty(),
            'owner_email' => $business->dynamicContent->nfcToken->user->email
        ]);
    }
}
```

## Workflows prioritarios para implementar

### **Fase 1: Monitoring & Alerts**
- Token scan notifications
- Inactivity alerts (tokens sin uso)
- Error notifications
- Performance monitoring

### **Fase 2: Business Automation**
- Auto-backup content
- Social media posting
- Customer retention emails
- Upselling campaigns

### **Fase 3: Advanced Integration**
- CRM synchronization
- Advanced analytics
- Multi-platform content distribution
- Customer success automation

## Casos de uso específicos por tipo de contenido

### **BUSINESS (Restaurantes)**
```
New Menu Item → n8n →
├── Update delivery platforms (Uber Eats, DoorDash)
├── Create Instagram story
├── Notify regular customers via email
└── Update inventory systems
```

### **TOURIST (Turismo)**
```
Weather API → n8n → 
├── Update tourist spot recommendations
├── Send weather alerts to visitors
├── Adjust content based on conditions
└── Notify tour operators
```

### **BUS_STOP (Transporte)**
```
Transport API → n8n →
├── Update real-time schedules
├── Send delay notifications
├── Update displays
└── Log service disruptions
```

### **EVENT (Eventos)**
```
Event Created → n8n →
├── Create calendar invites
├── Generate QR codes
├── Post to social media
├── Send to event platforms
└── Notify attendees
```

## Comandos de desarrollo

**Iniciar n8n:**
```bash
docker compose -f docker-compose.n8n.yml up -d
```

**Acceso a n8n:**
- URL: http://localhost:5678
- Usuario: admin
- Password: kraftdo123

**Webhooks endpoints disponibles:**
- Token scanned: `http://localhost:5678/webhook/token-scanned`
- User registered: `http://localhost:5678/webhook/user-registered`  
- Content updated: `http://localhost:5678/webhook/content-updated`

## Métricas de éxito

### **KPIs a trackear:**
1. **Retención de clientes**: % de clientes activos después de 30 días
2. **Engagement**: Promedio de escaneos por token por semana
3. **Conversión**: % de leads que se convierten en clientes pagadores
4. **Customer Success**: Tiempo de respuesta a problemas de clientes
5. **Crecimiento**: % de clientes que compran tokens adicionales

### **Alertas importantes:**
- Token sin escaneos por > 7 días
- Cliente sin login por > 14 días  
- Picos de actividad inusuales
- Errores en integraciones externas
- Tokens con alta popularidad (oportunidades de upsell)

## Integrations más valiosas

### **CRM/Sales:**
- HubSpot
- Pipedrive
- Salesforce

### **Communication:**
- WhatsApp Business API
- Slack
- Discord
- Telegram

### **Marketing:**
- Instagram/Facebook API
- Google My Business
- Mailchimp
- SendGrid

### **Analytics:**
- Google Analytics
- Google Sheets
- Tableau
- Metabase

### **Payment/Billing:**
- Stripe webhooks
- PayPal
- Mercado Pago

## Resumen ejecutivo

**n8n = Tu empleado virtual que:**
- Cuida a tus clientes 24/7
- Te avisa cuando hay problemas
- Genera más ventas automáticamente
- Te ahorra tiempo en tareas repetitivas

**ROI esperado:**
- 30% reducción en churn de clientes
- 50% aumento en customer success response time
- 25% incremento en upselling
- 80% reducción en tareas manuales de marketing

**Tiempo de implementación:**
- Fase 1: 1 semana (alertas básicas)
- Fase 2: 2 semanas (automation workflows)
- Fase 3: 1 mes (integrations avanzadas)