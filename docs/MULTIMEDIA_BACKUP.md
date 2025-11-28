# Gestión de Archivos Multimedia - Backup y Restauración

## Problema Resuelto

Anteriormente, cuando se hacía deploy a producción, se perdían todos los archivos multimedia porque el script eliminaba el volumen Docker `kraftdo-nfc-production_app_storage` que contiene todos los archivos subidos a través de Filament.

## Solución Implementada

1. **Backup automático**: Antes de cada deploy se crea un backup del volumen de storage
2. **Preservación de volúmenes**: Ya no se elimina el volumen `app_storage` durante deploys
3. **Restauración automática**: Si el deploy falla, se intenta restaurar el backup automáticamente
4. **Limpieza**: Se mantienen los últimos 5 backups automáticamente

## Comandos Manuales

### 1. Crear Backup Manual

```bash
# En el servidor de producción
BACKUP_DIR="/root/kraftdo-nfc-backups/manual_$(date +%Y%m%d_%H%M%S)"
mkdir -p $BACKUP_DIR

docker run --rm \
  -v kraftdo-nfc-production_app_storage:/source \
  -v $BACKUP_DIR:/backup \
  alpine tar czf /backup/storage.tar.gz -C /source .

echo "Backup creado en: $BACKUP_DIR/storage.tar.gz"
```

### 2. Restaurar desde Backup

```bash
# En el servidor de producción - Reemplaza FECHA con el backup deseado
BACKUP_DIR="/root/kraftdo-nfc-backups/FECHA"

# Detener contenedores
cd /root/docker-projects/kraftdo-nfc-production
docker-compose -f docker-compose.prod.yml down

# Eliminar volumen actual (CUIDADO: Esto borra archivos)
docker volume rm kraftdo-nfc-production_app_storage

# Crear nuevo volumen
docker volume create kraftdo-nfc-production_app_storage

# Restaurar archivos
docker run --rm \
  -v kraftdo-nfc-production_app_storage:/target \
  -v $BACKUP_DIR:/backup \
  alpine tar xzf /backup/storage.tar.gz -C /target

# Reiniciar contenedores
docker-compose -f docker-compose.prod.yml up -d
```

### 3. Listar Backups Disponibles

```bash
# En el servidor
ls -la /root/kraftdo-nfc-backups/
```

### 4. Verificar Archivos en Volumen

```bash
# Listar archivos en el volumen actual
docker run --rm -v kraftdo-nfc-production_app_storage:/data alpine find /data -type f | head -20
```

### 5. Copiar Archivos Específicos

```bash
# Copiar archivo específico desde volumen a servidor
docker run --rm -v kraftdo-nfc-production_app_storage:/data -v /tmp:/host alpine cp /data/public/gallery/archivo.jpg /host/

# Copiar archivo desde servidor a volumen
docker run --rm -v kraftdo-nfc-production_app_storage:/data -v /tmp:/host alpine cp /host/archivo.jpg /data/public/gallery/
```

## Estructura de Directorios en Storage

Los archivos multimedia se guardan en:

- `storage/app/public/gallery/` - Imágenes de galerías
- `storage/app/public/profiles/` - Imágenes de perfiles
- `storage/app/public/audio/` - Archivos de audio
- `storage/app/public/videos/` - Archivos de video
- `storage/app/public/uploads/` - Otros archivos subidos
- `storage/app/private/` - Archivos privados

## Monitoreo

Para verificar que los archivos se mantienen después del deploy:

```bash
# Antes del deploy
docker run --rm -v kraftdo-nfc-production_app_storage:/data alpine find /data -type f | wc -l

# Después del deploy (debería ser el mismo número o mayor)
docker run --rm -v kraftdo-nfc-production_app_storage:/data alpine find /data -type f | wc -l
```

## Configuración en Laravel

Los archivos se gestionan a través de:
- **Filesystem disk**: `public` (storage/app/public)
- **URL pública**: `/storage/*` (vía symlink)
- **Filament uploads**: Configurados para usar disk `public`

## Notas Importantes

1. Los backups se crean automáticamente en cada deploy a producción
2. Se mantienen los últimos 5 backups para conservar espacio
3. El volumen Docker persiste entre deploys para preservar archivos
4. Solo se eliminan cachés que pueden recrearse automáticamente