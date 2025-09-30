{{-- Enhanced Profile Action Buttons Component with Smart Contact Saving --}}
@props([
    'contentProfile' => null,
    'token' => null,
    'colors' => ['primary' => '#3B82F6', 'secondary' => '#8B5CF6', 'accent' => '#EC4899']
])

<div class="space-y-3 sm:space-y-4 animate-fade-in-up" style="animation-delay: 0.6s">
    {{-- Primary Action Button - Smart Contact Save --}}
    <button x-data="contactComponent()" 
            @click="saveContact()"
            id="saveContactBtn"
            class="w-full text-white px-6 py-3 sm:py-4 rounded-xl font-semibold hover:shadow-lg transform hover:scale-[1.02] transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 group relative overflow-hidden"
            style="background: linear-gradient(135deg, {{ $colors['primary'] }}, {{ $colors['secondary'] }}, {{ $colors['accent'] }}); focus-ring-color: {{ $colors['primary'] }};">
        <span class="relative z-10 flex items-center justify-center gap-3">
            <div class="w-5 h-5 transition-transform duration-200 group-hover:scale-110">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <span class="text-sm sm:text-base">Guardar Contacto</span>
        </span>
        <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-600"></div>
    </button>
</div>

<script>
    // Contact data from Laravel
    const contactInfo = {
        name: "{{ $contentProfile->name ?? $token->name ?? 'Contacto' }}",
        @if($contentProfile)
            @if($contentProfile->contact_email)
                email: "{{ $contentProfile->contact_email }}",
            @endif
            @if($contentProfile->contact_phone)
                phone: "{{ $contentProfile->contact_phone }}",
            @endif
            @if($contentProfile->contact_website)
                website: "{{ $contentProfile->contact_website }}",
            @endif
            @if($contentProfile->job_title)
                title: "{{ $contentProfile->job_title }}",
            @endif
            @if($contentProfile->bio)
                note: "{{ str_replace(["\n", "\r", '"'], ["\\n", "", "\'"], $contentProfile->bio) }}",
            @endif
        @endif
    };

    // Alpine.js component for Web Share API
    window.contactComponent = function() {
        return {
            saveContact() {
                const btn = document.getElementById('saveContactBtn');
                const originalContent = btn.innerHTML;
                
                setButtonState(btn, 'loading', 'Guardando...');
                
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
                                // Fallback to data URL method
                                this.tryDataURLMethod(vcard, btn, originalContent);
                            });
                            return;
                        }
                    } catch (error) {
                        // Continue to data URL method
                    }
                }
                
                // Method 2: Try iframe method (last attempt for native behavior)
                if (!this.tryIframeMethod(vcard, btn, originalContent)) {
                    // Method 3: Data URL method (simulates native behavior)
                    this.tryDataURLMethod(vcard, btn, originalContent);
                }
            },

            tryIframeMethod(vcard, btn, originalContent) {
                try {
                    // Create blob URL for vCard
                    const blob = new Blob([vcard], { type: 'text/vcard;charset=utf-8' });
                    const blobURL = URL.createObjectURL(blob);
                    
                    // Create hidden iframe to try to trigger native handler
                    const iframe = document.createElement('iframe');
                    iframe.style.display = 'none';
                    iframe.style.width = '0';
                    iframe.style.height = '0';
                    iframe.src = blobURL;
                    
                    document.body.appendChild(iframe);
                    
                    // Try alternative: set iframe src to data URL
                    setTimeout(() => {
                        const dataURL = `data:text/vcard;charset=utf-8,${encodeURIComponent(vcard)}`;
                        iframe.src = dataURL;
                    }, 100);
                    
                    // Try opening in new window as final iframe attempt
                    setTimeout(() => {
                        try {
                            const newWindow = window.open(blobURL, '_blank');
                            if (newWindow) {
                                newWindow.focus();
                                setTimeout(() => newWindow.close(), 1000);
                            }
                        } catch (e) {
                            // Ignore popup blocker errors
                        }
                    }, 200);
                    
                    // Clean up iframe after attempts
                    setTimeout(() => {
                        document.body.removeChild(iframe);
                        URL.revokeObjectURL(blobURL);
                    }, 2000);
                    
                    // Always return false to continue to next method
                    // (iframe method is experimental and may not work)
                    return false;
                    
                } catch (error) {
                    return false;
                }
            },

            tryDataURLMethod(vcard, btn, originalContent) {
                try {
                    // Create data URL that should trigger native contact handler
                    const dataURL = `data:text/vcard;charset=utf-8;base64,${btoa(unescape(encodeURIComponent(vcard)))}`;
                    
                    // Create temporary link to trigger native handler
                    const tempLink = document.createElement('a');
                    tempLink.href = dataURL;
                    tempLink.download = `${contactInfo.name.replace(/[^a-z0-9]/gi, '_')}.vcf`;
                    tempLink.style.display = 'none';
                    
                    document.body.appendChild(tempLink);
                    
                    // Try to open with native handler first
                    window.location.href = dataURL;
                    
                    // Fallback: trigger download if native handler doesn't work
                    setTimeout(() => {
                        tempLink.click();
                        document.body.removeChild(tempLink);
                        
                        // Show modal after data URL method as well
                        const modalBlob = new Blob([vcard], { type: 'text/vcard;charset=utf-8' });
                        const modalUrl = URL.createObjectURL(modalBlob);
                        setTimeout(() => this.showInstructionsModal(modalUrl), 500);
                    }, 500);
                    
                    setButtonState(btn, 'success', '¡Contacto guardado!');
                    setTimeout(() => setButtonState(btn, 'default', originalContent), 2500);
                    
                } catch (error) {
                    // Final fallback: traditional download
                    this.downloadVCardFallback(vcard, btn, originalContent);
                }
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
                    
                    // Update button state
                    setButtonState(btn, 'success', '¡Contacto descargado!');
                    setTimeout(() => setButtonState(btn, 'default', originalContent), 2500);
                    
                    // Create a NEW blob URL for the modal (separate from download)
                    const modalBlob = new Blob([vcard], { type: 'text/vcard;charset=utf-8' });
                    const modalUrl = URL.createObjectURL(modalBlob);
                    
                    // Show instructions modal after download with NEW vCard URL
                    setTimeout(() => this.showInstructionsModal(modalUrl), 1000);
                    
                } catch (error) {
                    console.error('Error downloading vCard:', error);
                    setButtonState(btn, 'error', 'Error al descargar');
                    setTimeout(() => setButtonState(btn, 'default', originalContent), 3000);
                }
            },

            showInstructionsModal(vcardUrl = null) {
                const modal = document.createElement('div');
                modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
                modal.style.backdropFilter = 'blur(4px)';
                
                const isAndroid = /Android/.test(navigator.userAgent);
                const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
                
                let instructions = '';
                let icon = '📱';
                
                if (isAndroid) {
                    instructions = `
                        <div class="space-y-3">
                            <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg">
                                <span class="text-2xl">📥</span>
                                <div>
                                    <div class="font-medium text-gray-900">1. Busca la notificación de descarga</div>
                                    <div class="text-sm text-gray-600">En la parte superior de tu pantalla</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg">
                                <span class="text-2xl">👆</span>
                                <div>
                                    <div class="font-medium text-gray-900">2. Toca la notificación</div>
                                    <div class="text-sm text-gray-600">Se abrirá automáticamente</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg">
                                <span class="text-2xl">📞</span>
                                <div>
                                    <div class="font-medium text-gray-900">3. Selecciona "Contactos"</div>
                                    <div class="text-sm text-gray-600">Android te preguntará con qué app abrir</div>
                                </div>
                            </div>
                        </div>
                    `;
                    icon = '🤖';
                } else if (isIOS) {
                    instructions = `
                        <div class="space-y-3">
                            <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg">
                                <span class="text-2xl">📥</span>
                                <div>
                                    <div class="font-medium text-gray-900">1. Ve a la app Archivos</div>
                                    <div class="text-sm text-gray-600">Busca en Descargas</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg">
                                <span class="text-2xl">👆</span>
                                <div>
                                    <div class="font-medium text-gray-900">2. Toca el archivo .vcf</div>
                                    <div class="text-sm text-gray-600">Archivo de contacto descargado</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg">
                                <span class="text-2xl">📞</span>
                                <div>
                                    <div class="font-medium text-gray-900">3. Se abrirá en Contactos</div>
                                    <div class="text-sm text-gray-600">Toca "Agregar contacto"</div>
                                </div>
                            </div>
                        </div>
                    `;
                    icon = '🍎';
                } else {
                    instructions = `
                        <div class="space-y-3">
                            <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg">
                                <span class="text-2xl">📥</span>
                                <div>
                                    <div class="font-medium text-gray-900">1. Busca el archivo descargado</div>
                                    <div class="text-sm text-gray-600">En tu carpeta de Descargas</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg">
                                <span class="text-2xl">👆</span>
                                <div>
                                    <div class="font-medium text-gray-900">2. Haz doble clic en el archivo .vcf</div>
                                    <div class="text-sm text-gray-600">Se abrirá con tu app de contactos</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg">
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
                    <div class="bg-white rounded-2xl w-full max-w-md mx-4 animate-slide-up-modal">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="text-3xl">${icon}</span>
                                <h3 class="text-xl font-bold text-gray-900">¡Casi listo!</h3>
                            </div>
                            <p class="text-gray-600">Sigue estos pasos para guardar el contacto:</p>
                        </div>
                        <div class="p-6">
                            ${instructions}
                        </div>
                        <div class="p-6 border-t border-gray-200">
                            <button onclick="this.closest('.fixed').remove(); ${vcardUrl ? `URL.revokeObjectURL('${vcardUrl}')` : ''}" 
                                    class="w-full py-3 px-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-200">
                                ¡Entendido!
                            </button>
                        </div>
                    </div>
                `;
                
                document.body.appendChild(modal);
                document.body.style.overflow = 'hidden';
                
                // Close on backdrop click
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        modal.remove();
                        document.body.style.overflow = '';
                        if (vcardUrl) {
                            URL.revokeObjectURL(vcardUrl);
                        }
                    }
                });
                
                // Auto close after 15 seconds and cleanup URL
                setTimeout(() => {
                    if (document.body.contains(modal)) {
                        modal.remove();
                        document.body.style.overflow = '';
                        if (vcardUrl) {
                            URL.revokeObjectURL(vcardUrl);
                        }
                    }
                }, 15000);
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



    // Set button state with visual feedback
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/>
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


</script>

<style>
    @keyframes fade-in-up {
        0% { opacity: 0; transform: translateY(10px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fade-in-up {
        animation: fade-in-up 0.6s ease-out;
    }
    
    @keyframes slide-up-modal {
        0% { opacity: 0; transform: translateY(20px) scale(0.95); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }
    
    .animate-slide-up-modal {
        animation: slide-up-modal 0.3s ease-out;
    }
</style>