-- Script de inicialización para MariaDB
-- Este script se ejecuta automáticamente cuando se crea el contenedor

-- Crear base de datos si no existe
CREATE DATABASE IF NOT EXISTS nfc_dynamic_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Crear usuario si no existe
CREATE USER IF NOT EXISTS 'nfc_user'@'%' IDENTIFIED BY 'nfc_password';

-- Otorgar permisos
GRANT ALL PRIVILEGES ON nfc_dynamic_db.* TO 'nfc_user'@'%';

-- Aplicar cambios
FLUSH PRIVILEGES;

-- Configuraciones adicionales para desarrollo
SET GLOBAL sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';
SET GLOBAL innodb_file_per_table = ON;
SET GLOBAL innodb_buffer_pool_size = 128M; 