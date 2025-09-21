#!/bin/bash

# =============================================================================
# SCRIPT DE OPTIMIZACIÓN PARA KRAFTDO NFC STAGING
# =============================================================================
# Crea directorios necesarios y configura optimizaciones para 1500-2000 usuarios

set -e

echo "🚀 Configurando optimizaciones para KraftDo NFC Staging..."

# Crear directorios necesarios
echo "📁 Creando directorios de optimización..."

# Directorio para Redis
sudo mkdir -p /opt/docker/redis/staging
sudo chown 999:999 /opt/docker/redis/staging

# Directorios para logs de PHP
mkdir -p logs/php
chmod 755 logs/php

# Directorios para cache temporal
sudo mkdir -p /tmp/opcache
sudo chmod 755 /tmp/opcache

echo "✅ Directorios creados correctamente"

# Configurar límites del sistema
echo "⚙️  Configurando límites del sistema..."

# Crear archivo de configuración para límites
sudo tee /etc/security/limits.d/kraftdo-nfc.conf > /dev/null <<EOF
# Límites optimizados para KraftDo NFC
www-data soft nofile 65536
www-data hard nofile 65536
nginx soft nofile 65536  
nginx hard nofile 65536
EOF

# Configurar sysctl para networking
sudo tee /etc/sysctl.d/99-kraftdo-nfc.conf > /dev/null <<EOF
# Optimizaciones de red para KraftDo NFC
net.core.somaxconn = 65535
net.ipv4.tcp_keepalive_time = 600
net.ipv4.tcp_keepalive_intvl = 30
net.ipv4.tcp_keepalive_probes = 3
net.ipv4.tcp_fin_timeout = 30
net.ipv4.tcp_tw_reuse = 1
net.core.rmem_max = 134217728
net.core.wmem_max = 134217728
net.ipv4.tcp_rmem = 4096 65536 134217728
net.ipv4.tcp_wmem = 4096 65536 134217728
vm.swappiness = 1
EOF

echo "📊 Aplicando configuraciones del sistema..."
sudo sysctl -p /etc/sysctl.d/99-kraftdo-nfc.conf

echo "🎉 Optimizaciones aplicadas correctamente!"
echo "📈 El sistema está optimizado para 1500-2000 usuarios concurrentes"
echo "💡 Recuerda hacer deploy de los cambios en Docker Compose"