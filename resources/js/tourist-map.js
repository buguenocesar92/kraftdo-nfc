/**
 * Tourist Map Component for Alpine.js
 * Maneja el mapa interactivo con Leaflet para landing pages turísticas
 */

document.addEventListener('alpine:init', () => {
    Alpine.data('touristMap', (mapData) => ({
        map: null,
        markers: [],
        
        initMap() {
            if (!mapData.center || !mapData.center.lat || !mapData.center.lng) {
                console.warn('Tourist map: No valid coordinates provided');
                return;
            }

            // Inicializar el mapa
            this.map = L.map('tourist-map').setView(
                [mapData.center.lat, mapData.center.lng], 
                mapData.zoom || 15
            );

            // Agregar capa de tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19,
            }).addTo(this.map);

            // Agregar marcador principal
            if (mapData.mainMarker) {
                this.addMainMarker(mapData.mainMarker);
            }

            // Agregar marcadores de lugares cercanos
            if (mapData.nearbySpots && mapData.nearbySpots.length > 0) {
                mapData.nearbySpots.forEach(spot => {
                    this.addNearbyMarker(spot);
                });

                // Ajustar vista para incluir todos los marcadores
                this.fitAllMarkers();
            }
        },

        addMainMarker(markerData) {
            // Crear icono personalizado para el marcador principal
            const mainIcon = L.divIcon({
                html: `
                    <div class="relative">
                        <div class="w-8 h-8 bg-red-500 rounded-full border-4 border-white shadow-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                            </svg>
                        </div>
                        <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-2 border-r-2 border-t-2 border-transparent border-t-red-500"></div>
                    </div>
                `,
                className: 'tourist-main-marker',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });

            const marker = L.marker([markerData.lat, markerData.lng], {
                icon: mainIcon,
                title: markerData.title
            }).addTo(this.map);

            // Popup para el marcador principal
            const popupContent = `
                <div class="tourist-popup-main">
                    <h3 class="font-bold text-lg text-gray-900 mb-2">${markerData.title}</h3>
                    ${markerData.description ? `<p class="text-gray-600 text-sm mb-3">${markerData.description}</p>` : ''}
                    <div class="flex space-x-2">
                        <button onclick="window.open('https://maps.google.com/?q=${markerData.lat},${markerData.lng}', '_blank')" 
                                class="px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600 transition-colors">
                            Ver en Maps
                        </button>
                    </div>
                </div>
            `;

            marker.bindPopup(popupContent, {
                maxWidth: 300,
                className: 'tourist-popup'
            });

            this.markers.push(marker);
            
            // Abrir popup automáticamente
            marker.openPopup();
        },

        addNearbyMarker(spotData) {
            // Crear icono personalizado basado en el tipo
            const iconHtml = this.createSpotIcon(spotData);
            
            const spotIcon = L.divIcon({
                html: iconHtml,
                className: 'tourist-spot-marker',
                iconSize: [24, 24],
                iconAnchor: [12, 24],
                popupAnchor: [0, -24]
            });

            const marker = L.marker([spotData.lat, spotData.lng], {
                icon: spotIcon,
                title: spotData.title
            }).addTo(this.map);

            // Popup para lugares cercanos
            const popupContent = this.createSpotPopup(spotData);

            marker.bindPopup(popupContent, {
                maxWidth: 250,
                className: 'tourist-popup'
            });

            this.markers.push(marker);
        },

        createSpotIcon(spotData) {
            const color = spotData.color || '#3B82F6';
            const iconName = this.getIconSvg(spotData.icon || 'map-pin');
            
            return `
                <div class="relative">
                    <div class="w-6 h-6 rounded-full border-2 border-white shadow-lg flex items-center justify-center" 
                         style="background-color: ${color}">
                        ${iconName}
                    </div>
                    <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-1 border-r-1 border-t-1 border-transparent" 
                         style="border-top-color: ${color}"></div>
                </div>
            `;
        },

        createSpotPopup(spotData) {
            let popupHtml = `
                <div class="tourist-popup-spot">
                    <h4 class="font-semibold text-gray-900 mb-1">${spotData.title}</h4>
                    <p class="text-xs text-gray-500 mb-2">${this.getTypeLabel(spotData.type)}</p>
            `;

            if (spotData.description) {
                popupHtml += `<p class="text-gray-600 text-sm mb-2">${spotData.description}</p>`;
            }

            if (spotData.distance) {
                popupHtml += `<p class="text-gray-500 text-xs mb-2">📍 ${spotData.distance} km</p>`;
            }

            // Información adicional
            if (spotData.additional_info && Object.keys(spotData.additional_info).length > 0) {
                popupHtml += '<div class="border-t pt-2 mt-2">';
                Object.entries(spotData.additional_info).forEach(([key, value]) => {
                    if (value) {
                        popupHtml += `<p class="text-xs text-gray-600"><span class="font-medium">${key}:</span> ${value}</p>`;
                    }
                });
                popupHtml += '</div>';
            }

            popupHtml += `
                    <div class="flex space-x-1 mt-3">
                        <button onclick="window.open('https://maps.google.com/?q=${spotData.lat},${spotData.lng}', '_blank')" 
                                class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded hover:bg-gray-200 transition-colors">
                            Maps
                        </button>
                    </div>
                </div>
            `;

            return popupHtml;
        },

        getIconSvg(iconName) {
            const icons = {
                'map-pin': '<svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>',
                'utensils': '<svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M8.1 13.34l2.83-2.83L3.91 3.5a4.008 4.008 0 000 5.66l4.19 4.18zm6.78-1.81c1.53.71 3.68.21 5.27-1.38 1.91-1.91 2.28-4.65.81-6.12-1.46-1.46-4.20-1.10-6.12.81-1.59 1.59-2.09 3.74-1.38 5.27L3.7 19.87l1.41 1.41L12 14.41l6.88 6.88 1.41-1.41-6.88-6.88 1.37-1.37z"/></svg>',
                'bed': '<svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M7 14c1.66 0 3-1.34 3-3S8.66 8 7 8s-3 1.34-3 3 1.34 3 3 3zm0-4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm12-3h-8v8H3V9H1v11h2v-3h18v3h2v-7c0-2.21-1.79-4-4-4z"/></svg>',
                'bus': '<svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M4 16c0 .88.39 1.67 1 2.22V20a1 1 0 001 1h1a1 1 0 001-1v-1h8v1a1 1 0 001 1h1a1 1 0 001-1v-1.78c.61-.55 1-1.34 1-2.22V6c0-3.5-3.58-4-8-4s-8 .5-8 4v10zm3.5 1c-.83 0-1.5-.67-1.5-1.5S6.67 14 7.5 14s1.5.67 1.5 1.5S8.33 17 7.5 17zm9 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm1.5-6H6V6h12v5z"/></svg>',
                'camera': '<svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 15.2l3.536-3.536 1.414 1.414L12 18.028l-4.95-4.95 1.414-1.414L12 15.2z"/><path d="M17 9l-2-3H9L7 9H4c-1.1 0-2 .9-2 2v9c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2v-9c0-1.1-.9-2-2-2h-3zM12 19c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"/></svg>',
                'shopping-bag': '<svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M19 7h-3V6a4 4 0 0 0-8 0v1H5a1 1 0 0 0-1 1v11a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V8a1 1 0 0 0-1-1zM10 6a2 2 0 0 1 4 0v1h-4V6zm8 15a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V9h2v1a1 1 0 0 0 2 0V9h4v1a1 1 0 0 0 2 0V9h2v12z"/></svg>',
                'wrench': '<svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.4 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4z"/></svg>',
                'plus-circle': '<svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/></svg>',
                'credit-card': '<svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg>'
            };
            
            return icons[iconName] || icons['map-pin'];
        },

        getTypeLabel(type) {
            const labels = {
                'restaurante': 'Restaurante',
                'hotel': 'Hotel/Alojamiento',
                'transporte': 'Transporte',
                'atraccion': 'Atracción Turística',
                'comercio': 'Comercio/Tienda',
                'servicio': 'Servicio',
                'salud': 'Centro de Salud',
                'banco': 'Banco/ATM'
            };
            
            return labels[type] || type.charAt(0).toUpperCase() + type.slice(1);
        },

        fitAllMarkers() {
            if (this.markers.length > 1) {
                const group = new L.featureGroup(this.markers);
                this.map.fitBounds(group.getBounds().pad(0.1));
            }
        },

        // Método público para agregar marcadores dinámicamente
        addMarker(lat, lng, title, options = {}) {
            const marker = L.marker([lat, lng], {
                title: title,
                ...options
            }).addTo(this.map);
            
            this.markers.push(marker);
            return marker;
        },

        // Método público para limpiar todos los marcadores
        clearMarkers() {
            this.markers.forEach(marker => {
                this.map.removeLayer(marker);
            });
            this.markers = [];
        },

        // Destructor para limpiar recursos
        destroy() {
            if (this.map) {
                this.map.remove();
                this.map = null;
            }
            this.markers = [];
        }
    }));
});

// Estilos CSS adicionales para los popups (se pueden mover a un archivo CSS separado)
const touristMapStyles = `
    .tourist-popup .leaflet-popup-content-wrapper {
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    .tourist-popup .leaflet-popup-content {
        margin: 12px 16px;
        font-family: 'Inter', system-ui, sans-serif;
    }
    
    .tourist-popup-main h3 {
        color: #1f2937;
        margin-bottom: 8px;
    }
    
    .tourist-popup-spot h4 {
        color: #1f2937;
        margin-bottom: 4px;
    }
    
    .tourist-main-marker,
    .tourist-spot-marker {
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
    }
    
    .leaflet-popup-close-button {
        font-size: 18px !important;
        font-weight: bold !important;
        color: #6b7280 !important;
    }
    
    .leaflet-popup-close-button:hover {
        color: #374151 !important;
    }
`;

// Inyectar estilos
if (!document.getElementById('tourist-map-styles')) {
    const styleSheet = document.createElement('style');
    styleSheet.id = 'tourist-map-styles';
    styleSheet.textContent = touristMapStyles;
    document.head.appendChild(styleSheet);
}