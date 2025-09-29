# Storage Symlink Issue - Video Upload 404 Error

## Problema

Los videos subidos a través de Livewire FileUpload no eran accesibles en el entorno de staging, generando errores 404:

```
Failed to load resource: the server responded with a status of 404 ()
/storage/videos/Kesenai%20Tsumi%20-%20Nana%20Kitade%20(1080p,%20h264).mp4:1
```

## Diagnóstico

### 1. Configuración inicial de Livewire

El problema comenzó con el límite de tamaño de archivos de Livewire (12MB por defecto). Los videos grandes no se podían subir.

**Solución aplicada en `config/livewire.php`:**

```php
'temporary_file_upload' => [
    'rules' => ['required', 'file', 'max:102400'], // Aumentado a 100MB
    // ... otras configuraciones
],
```

### 2. Configuración PHP

También era necesario ajustar los límites de PHP para soportar archivos de 100MB:

```ini
upload_max_filesize = 100M
post_max_size = 110M
```

### 3. Problema principal: Docker Volume Mount

El issue real estaba en la configuración de Docker Compose. El archivo `docker-compose.staging.yml` tenía un mount que sobrescribía el enlace simbólico:

```yaml
volumes:
  # PROBLEMÁTICO: Este mount sobrescribía el symlink interno
  - ./public/storage:/var/www/html/public/storage
```

### 4. Diagnóstico del problema

**Archivos existían físicamente:**
```bash
$ ls -la storage/app/public/videos/
-rw-r--r-- 1 nginx nginx 8410563 Sep 29 17:36 Kesenai Tsumi - Nana Kitade (1080p, h264).mp4
```

**Pero el enlace simbólico estaba roto:**
```bash
$ ls -la public/storage/videos/
ls: public/storage/videos/: No such file or directory
```

**El directorio era real en lugar de un symlink:**
```bash
$ ls -la public/storage
drwxr-xr-x 2 root root 4096 Sep 29 17:24 .
```

## Solución

### 1. Eliminar el mount problemático

En `docker-compose.staging.yml`:

```yaml
volumes:
  - app_storage:/var/www/html/storage
  - app_bootstrap_cache:/var/www/html/bootstrap/cache
  # Storage directory will be symlinked internally by Laravel
  # - ./public/storage:/var/www/html/public/storage  # ← Comentado/eliminado
```

### 2. Mejorar el workflow de deployment

En `.github/workflows/deploy.yml`:

```yaml
# Create storage link if needed
echo "🔗 Creating storage link..."
docker-compose -f docker-compose.staging.yml exec -T web bash -c "
  # Remove any existing public/storage directory/link
  rm -rf public/storage || true
  # Create the symbolic link manually if Laravel command fails
  php artisan storage:link || ln -sfn /var/www/html/storage/app/public /var/www/html/public/storage
  # Verify the link works
  ls -la public/storage/
"
```

### 3. Verificación de la solución

Después de aplicar los cambios:

```bash
$ ls -la public/storage/videos/
-rw-r--r-- 1 nginx nginx 8410563 Sep 29 17:36 Kesenai Tsumi - Nana Kitade (1080p, h264).mp4
```

## Lecciones aprendidas

1. **Docker mounts vs symlinks**: Los volume mounts de Docker pueden sobrescribir enlaces simbólicos internos del contenedor.

2. **Laravel storage:link**: El comando `php artisan storage:link` crea un enlace simbólico de `public/storage` a `storage/app/public`, pero esto se puede romper con configuraciones de Docker mal configuradas.

3. **Debugging en contenedores**: Usar `docker exec -it container bash` para inspeccionar el estado interno del contenedor es crucial para diagnosticar este tipo de problemas.

4. **Workflow robustez**: Es importante que los workflows de deployment manejen casos edge como enlaces simbólicos rotos.

## Archivos modificados

- `config/livewire.php`: Aumentar límite de archivos a 100MB
- `docker-compose.staging.yml`: Eliminar mount problemático 
- `.github/workflows/deploy.yml`: Mejorar creación de symlink

## Comandos de verificación

Para verificar que todo funciona correctamente:

```bash
# Dentro del contenedor
docker exec -it kraftdo-nfc-staging-web bash

# Verificar que el symlink existe y funciona
ls -la public/storage/videos/

# Verificar que los archivos son accesibles vía web
curl -I https://staging.kraftdo.cl/storage/videos/archivo.mp4
```