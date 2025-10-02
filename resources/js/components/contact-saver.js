/**
 * Contact Saver Component
 * Alpine.js component for vCard generation and sharing that mimics original functionality
 */

// Test if this file is loading
alert('ARCHIVO CARGADO: contact-saver.js se está ejecutando');

// Alpine.js component factory function - EXACT replica of working version
window.contactComponent = function(contactData) {
    alert('FUNCIÓN LLAMADA: contactComponent ejecutándose con datos: ' + JSON.stringify(contactData));
    // If no contactData provided, create empty object
    const contactInfo = contactData || {};
    
    return {
        saveContact() {
            alert('1. Función saveContact ejecutada');
            
            const btn = document.getElementById('saveContactBtn');
            if (!btn) {
                alert('2. ERROR: No se encontró el botón');
                return;
            }
            
            alert(`3. contactInfo recibido: ${JSON.stringify(contactInfo)}`);
            
            if (!contactInfo || !contactInfo.name) {
                alert('4. ERROR: Sin datos de contacto válidos');
                return;
            }
            
            alert(`5. OK: Datos válidos para ${contactInfo.name}`);
            
            const originalContent = btn.innerHTML;
            setButtonState(btn, 'loading', 'Guardando...');
            
            const vcard = this.generateVCard();
            alert(`6. vCard generado: ${vcard.substring(0, 100)}...`);
            
            this.actualSaveContact(btn, originalContent);
        },
        
        actualSaveContact(btn, originalContent) {
            const vcard = this.generateVCard();
            const isMobile = /Android|iPhone|iPad|iPod/.test(navigator.userAgent);
            
            // Method 1: Try Web Share API first (mobile only)
            if (isMobile && navigator.share) {
                // Try to share vCard file directly
                try {
                    const file = new File([vcard], `${contactInfo.name.replace(/[^a-z0-9]/gi, '_')}.vcf`, {
                        type: 'text/vcard'
                    });
                    
                    if (navigator.canShare && navigator.canShare({ files: [file] })) {
                        navigator.share({
                            files: [file],
                            title: `Contacto: ${contactInfo.name}`
                        }).then(() => {
                            setButtonState(btn, 'success', '¡Contacto guardado!');
                            setTimeout(() => setButtonState(btn, 'default', originalContent), 2500);
                        }).catch((error) => {
                            if (error.name === 'AbortError') {
                                setButtonState(btn, 'default', originalContent);
                                return;
                            }
                            // Fallback to download method
                            this.downloadVCardFallback(vcard, btn, originalContent);
                        });
                        return;
                    }
                } catch (error) {
                    // Continue to download method
                }
            }
            
            // Method 2: Direct download (most reliable)
            this.downloadVCardFallback(vcard, btn, originalContent);
        },

        downloadVCardFallback(vcard, btn, originalContent) {
            try {
                // Create blob and URL for download
                const blob = new Blob([vcard], { type: 'text/vcard;charset=utf-8' });
                const downloadUrl = URL.createObjectURL(blob);
                
                // Trigger download
                const link = document.createElement('a');
                link.href = downloadUrl;
                link.download = `${contactInfo.name.replace(/[^a-z0-9]/gi, '_')}.vcf`;
                link.style.display = 'none';
                
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Clean up download URL immediately
                setTimeout(() => URL.revokeObjectURL(downloadUrl), 100);
                
                // Create a NEW blob URL for the modal (separate from download)
                const modalBlob = new Blob([vcard], { type: 'text/vcard;charset=utf-8' });
                const modalUrl = URL.createObjectURL(modalBlob);
                
                // Update button state first
                setButtonState(btn, 'success', '¡Contacto descargado!');
                
                // Show instructions modal after download with NEW vCard URL
                // Wait longer to ensure download popup is gone
                setTimeout(() => {
                    this.showInstructionsModal(modalUrl);
                    // Reset button state after modal appears
                    setTimeout(() => setButtonState(btn, 'default', originalContent), 500);
                }, 2000);
                
            } catch (error) {
                console.error('Error downloading vCard:', error);
                setButtonState(btn, 'error', 'Error al descargar');
                setTimeout(() => setButtonState(btn, 'default', originalContent), 3000);
            }
        },

        showInstructionsModal(vcardUrl = null) {
            const modal = document.createElement('div');
            modal.className = 'contact-modal-backdrop';
            
            const isAndroid = /Android/.test(navigator.userAgent);
            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
            
            let instructions = '';
            let icon = '📱';
            
            if (isAndroid) {
                instructions = `
                    <div class="space-y-3">
                        <div class="contact-instruction-step blue">
                            <span class="text-2xl">📥</span>
                            <div>
                                <div class="font-medium text-gray-900">1. Si no se abre automáticamente, busca la notificación de descarga</div>
                                <div class="text-sm text-gray-600">En la parte superior de tu pantalla</div>
                            </div>
                        </div>
                        <div class="contact-instruction-step blue">
                            <span class="text-2xl">👆</span>
                            <div>
                                <div class="font-medium text-gray-900">2. Toca la notificación o el archivo descargado</div>
                                <div class="text-sm text-gray-600">También puedes ir a Descargas</div>
                            </div>
                        </div>
                        <div class="contact-instruction-step green">
                            <span class="text-2xl">📞</span>
                            <div>
                                <div class="font-medium text-gray-900">3. Selecciona "Contactos" si te pregunta</div>
                                <div class="text-sm text-gray-600">Android elegirá automáticamente en la mayoría de casos</div>
                            </div>
                        </div>
                    </div>
                `;
                icon = '🤖';
            } else if (isIOS) {
                instructions = `
                    <div class="space-y-3">
                        <div class="contact-instruction-step blue">
                            <span class="text-2xl">📥</span>
                            <div>
                                <div class="font-medium text-gray-900">1. Si no se abre automáticamente, ve a la app Archivos</div>
                                <div class="text-sm text-gray-600">Busca en Descargas o revisa las notificaciones</div>
                            </div>
                        </div>
                        <div class="contact-instruction-step blue">
                            <span class="text-2xl">👆</span>
                            <div>
                                <div class="font-medium text-gray-900">2. Toca el archivo .vcf descargado</div>
                                <div class="text-sm text-gray-600">En versiones nuevas puede abrirse automáticamente</div>
                            </div>
                        </div>
                        <div class="contact-instruction-step green">
                            <span class="text-2xl">📞</span>
                            <div>
                                <div class="font-medium text-gray-900">3. Se abrirá en Contactos</div>
                                <div class="text-sm text-gray-600">Toca "Agregar contacto" si aparece</div>
                            </div>
                        </div>
                    </div>
                `;
                icon = '🍎';
            } else {
                instructions = `
                    <div class="space-y-3">
                        <div class="contact-instruction-step blue">
                            <span class="text-2xl">📥</span>
                            <div>
                                <div class="font-medium text-gray-900">1. Busca el archivo descargado</div>
                                <div class="text-sm text-gray-600">En tu carpeta de Descargas</div>
                            </div>
                        </div>
                        <div class="contact-instruction-step blue">
                            <span class="text-2xl">👆</span>
                            <div>
                                <div class="font-medium text-gray-900">2. Haz doble clic en el archivo .vcf</div>
                                <div class="text-sm text-gray-600">Se abrirá con tu app de contactos</div>
                            </div>
                        </div>
                        <div class="contact-instruction-step green">
                            <span class="text-2xl">📞</span>
                            <div>
                                <div class="font-medium text-gray-900">3. Confirma agregar contacto</div>
                                <div class="text-sm text-gray-600">En tu aplicación de contactos</div>
                            </div>
                        </div>
                    </div>
                `;
                icon = '💻';
            }
            
            modal.innerHTML = `
                <div class="contact-modal-content animate-slide-up-modal">
                    <div class="contact-modal-header">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-3xl">${icon}</span>
                            <h3 class="text-xl font-bold text-gray-900">¡Casi listo!</h3>
                        </div>
                        <div class="contact-note-box">
                            <p class="text-sm text-blue-800">
                                <span class="font-medium">💡 Nota:</span> En algunos dispositivos la app Contactos se abrirá automáticamente. Si no es tu caso, sigue estos pasos:
                            </p>
                        </div>
                        <p class="text-gray-600">Pasos para guardar el contacto manualmente:</p>
                    </div>
                    <div class="contact-modal-body">
                        ${instructions}
                    </div>
                    <div class="contact-modal-footer">
                        <button onclick="this.closest('.contact-modal-backdrop').remove(); document.body.style.overflow = ''; ${vcardUrl ? `URL.revokeObjectURL('${vcardUrl}')` : ''}" 
                                class="contact-button-primary">
                            ¡Entendido!
                        </button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            document.body.style.overflow = 'hidden';
            
            // Close on backdrop click (but prevent accidental closure)
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.remove();
                    document.body.style.overflow = '';
                    if (vcardUrl) {
                        URL.revokeObjectURL(vcardUrl);
                    }
                }
            });

            // Prevent modal from closing due to focus/blur events
            modal.addEventListener('focusout', (e) => {
                e.stopPropagation();
            });

            modal.addEventListener('blur', (e) => {
                e.stopPropagation();
            });

            // Prevent window events from closing modal
            const preventClose = (e) => {
                e.stopPropagation();
            };

            window.addEventListener('blur', preventClose);
            window.addEventListener('focus', preventClose);
            document.addEventListener('visibilitychange', preventClose);
            
            // Cleanup window listeners when modal is removed
            const originalRemove = modal.remove;
            modal.remove = function() {
                window.removeEventListener('blur', preventClose);
                window.removeEventListener('focus', preventClose);
                document.removeEventListener('visibilitychange', preventClose);
                originalRemove.call(this);
            };
            
            // Auto close after 20 seconds and cleanup URL (increased time)
            setTimeout(() => {
                if (document.body.contains(modal)) {
                    modal.remove();
                    document.body.style.overflow = '';
                    if (vcardUrl) {
                        URL.revokeObjectURL(vcardUrl);
                    }
                }
            }, 20000);
        },

        generateVCard() {
            const contact = contactInfo;
            let vcard = "BEGIN:VCARD\n";
            vcard += "VERSION:3.0\n";
            vcard += `FN:${contact.name}\n`;
            
            const nameParts = contact.name.split(' ');
            const firstName = nameParts[0] || '';
            const lastName = nameParts.slice(1).join(' ') || '';
            vcard += `N:${lastName};${firstName};;;\n`;
            
            if (contact.title) {
                vcard += `ORG:${contact.title}\n`;
            }
            
            if (contact.phone) {
                vcard += `TEL;CELL:${contact.phone}\n`;
            }
            
            if (contact.email) {
                vcard += `EMAIL:${contact.email}\n`;
            }
            
            if (contact.website) {
                vcard += `URL:${contact.website}\n`;
            }
            
            if (contact.note) {
                vcard += `ADR;WORK:;;${contact.note};;;;\n`;
            }
            
            vcard += "END:VCARD";
            return vcard;
        }
    }
}

// Set button state with visual feedback - EXACT replica
function setButtonState(btn, state, text) {
    const states = {
        loading: {
            icon: `<div class="w-5 h-5 animate-spin">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                  </div>`,
            disabled: true
        },
        success: {
            icon: `<div class="w-5 h-5 text-green-300">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                  </div>`,
            disabled: true
        },
        error: {
            icon: `<div class="w-5 h-5 text-red-300">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                  </div>`,
            disabled: true
        },
        default: {
            icon: `<div class="w-5 h-5 transition-transform duration-200 group-hover:scale-110">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 616 0z"/>
                    </svg>
                  </div>`,
            disabled: false
        }
    };

    const currentState = states[state] || states.default;
    
    if (state === 'default') {
        btn.innerHTML = text;
    } else {
        btn.innerHTML = `
            <span class="relative z-10 flex items-center justify-center gap-3">
                ${currentState.icon}
                <span class="text-sm sm:text-base">${text}</span>
            </span>
        `;
    }
    
    btn.disabled = currentState.disabled;
}