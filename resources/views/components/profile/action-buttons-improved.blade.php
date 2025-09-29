{{-- Enhanced Profile Action Buttons Component with Smart Contact Saving --}}
@props([
    'contentProfile' => null,
    'token' => null,
    'colors' => ['primary' => '#3B82F6', 'secondary' => '#8B5CF6', 'accent' => '#EC4899']
])

<div class="space-y-3 sm:space-y-4 animate-fade-in-up" style="animation-delay: 0.6s">
    {{-- Primary Action Button - Smart Contact Save --}}
    <button onclick="smartSaveContact()" 
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

    // Smart contact saving with device detection
    async function smartSaveContact() {
        const btn = document.getElementById('saveContactBtn');
        const originalContent = btn.innerHTML;
        
        // Show loading state
        setButtonState(btn, 'loading', 'Guardando...');
        
        try {
            const userAgent = navigator.userAgent.toLowerCase();
            const isIOS = /iphone|ipad|ipod/.test(userAgent);
            const isAndroid = /android/.test(userAgent);
            const isMobile = isIOS || isAndroid;
            
            // Debug information
            console.log('Device detection:', { isIOS, isAndroid, isMobile, hasShare: !!navigator.share, hasCanShare: !!navigator.canShare });
            
            // Try different methods based on device and capabilities
            let success = false;
            
            // Method 1: iOS specific handling (prioritize over Web Share API)
            if (isIOS) {
                console.log('Trying iOS integration...');
                // Force iOS to always show action sheet if we have contact data
                if (contactInfo.phone || contactInfo.email) {
                    console.log('iOS - forcing action sheet due to contact data');
                    showMobileActionSheet();
                    success = true;
                } else {
                    success = await tryiOSIntegration();
                }
                console.log('iOS integration result:', success);
            }
            
            // Method 2: Android specific handling  
            else if (isAndroid) {
                console.log('Trying Android integration...');
                success = await tryAndroidIntegration();
                console.log('Android integration result:', success);
            }
            
            // Method 3: Web Share API (for other modern mobile browsers)
            else if (isMobile && navigator.share && navigator.canShare) {
                console.log('Trying Web Share API...');
                success = await tryWebShareAPI();
                console.log('Web Share API result:', success);
            }
            
            // Method 4: Fallback - Enhanced vCard download
            if (!success) {
                console.log('Using fallback vCard download...');
                success = await downloadEnhancedVCard();
                console.log('Fallback result:', success);
            }
            
            if (success) {
                setButtonState(btn, 'success', '¡Contacto guardado!');
                setTimeout(() => setButtonState(btn, 'default', originalContent), 2500);
            } else {
                throw new Error('No se pudo guardar el contacto');
            }
            
        } catch (error) {
            console.error('Error saving contact:', error);
            setButtonState(btn, 'error', 'Error al guardar');
            setTimeout(() => setButtonState(btn, 'default', originalContent), 2500);
        }
    }

    // Try Web Share API with vCard file
    async function tryWebShareAPI() {
        try {
            const vcard = generateVCard(contactInfo);
            const file = new File([vcard], `${sanitizeFileName(contactInfo.name)}.vcf`, {
                type: 'text/vcard'
            });
            
            if (navigator.canShare({ files: [file] })) {
                await navigator.share({
                    files: [file],
                    title: `Contacto: ${contactInfo.name}`,
                    text: `Guarda el contacto de ${contactInfo.name} en tu teléfono`
                });
                return true;
            }
        } catch (error) {
            console.log('Web Share API failed:', error);
        }
        return false;
    }

    // iOS specific integration
    async function tryiOSIntegration() {
        console.log('iOS integration - contactInfo:', contactInfo);
        console.log('iOS integration - has phone or email:', !!(contactInfo.phone || contactInfo.email));
        
        // iOS doesn't support Web Share API with files well
        // So we create an action sheet with native app links
        if (contactInfo.phone || contactInfo.email) {
            console.log('iOS - showing action sheet');
            showMobileActionSheet();
            return true;
        }
        
        console.log('iOS - no phone or email, returning false');
        return false;
    }

    // Android specific integration
    async function tryAndroidIntegration() {
        // For Android, show action sheet like iOS since intents are unreliable
        if (contactInfo.phone || contactInfo.email) {
            showMobileActionSheet();
            return true;
        }
        
        // Try Web Share API as fallback
        try {
            if (navigator.share) {
                const vcard = generateVCard(contactInfo);
                await navigator.share({
                    title: `Contacto: ${contactInfo.name}`,
                    text: `Información de contacto de ${contactInfo.name}`,
                    url: 'data:text/vcard;charset=utf-8,' + encodeURIComponent(vcard)
                });
                return true;
            }
        } catch (error) {
            console.log('Android Web Share failed:', error);
        }
        
        return false;
    }

    // Show mobile action sheet for iOS/Android
    function showMobileActionSheet() {
        console.log('showMobileActionSheet called with contactInfo:', contactInfo);
        
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-end justify-center';
        modal.style.backdropFilter = 'blur(4px)';
        
        const actions = [];
        
        if (contactInfo.phone) {
            console.log('Adding phone actions for:', contactInfo.phone);
            actions.push({
                icon: '📞',
                title: 'Llamar',
                subtitle: contactInfo.phone,
                action: () => window.open(`tel:${contactInfo.phone}`, '_self')
            });
            
            actions.push({
                icon: '💬',
                title: 'WhatsApp',
                subtitle: 'Enviar mensaje',
                action: () => window.open(`https://wa.me/${contactInfo.phone.replace(/[^0-9]/g, '')}`, '_blank')
            });
            
            actions.push({
                icon: '💬',
                title: 'SMS',
                subtitle: 'Enviar SMS',
                action: () => window.open(`sms:${contactInfo.phone}`, '_self')
            });
        }
        
        if (contactInfo.email) {
            actions.push({
                icon: '📧',
                title: 'Email',
                subtitle: contactInfo.email,
                action: () => window.open(`mailto:${contactInfo.email}`, '_self')
            });
        }
        
        // Add Android-specific contact actions
        const isAndroid = /android/.test(navigator.userAgent.toLowerCase());
        
        if (isAndroid) {
            actions.push({
                icon: '📱',
                title: 'Google Contactos',
                subtitle: 'Abrir app',
                action: () => {
                    try {
                        // Try Google Contacts web app
                        window.open('https://contacts.google.com/', '_blank');
                    } catch (e) {
                        // Fallback to download
                        downloadEnhancedVCard();
                    }
                    closeMobileActionSheet();
                }
            });
        }
        
        actions.push({
            icon: '📇',
            title: 'Descargar vCard',
            subtitle: 'Archivo de contacto',
            action: () => {
                downloadEnhancedVCard();
                closeMobileActionSheet();
            }
        });
        
        // Add copy contact info option
        if (contactInfo.phone || contactInfo.email) {
            actions.push({
                icon: '📋',
                title: 'Copiar información',
                subtitle: 'Al portapapeles',
                action: () => {
                    copyContactInfo();
                    closeMobileActionSheet();
                }
            });
        }

        modal.innerHTML = `
            <div class="bg-white rounded-t-3xl w-full max-w-md mx-4 animate-slide-up">
                <div class="p-4 border-b border-gray-200">
                    <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-4"></div>
                    <h3 class="text-lg font-semibold text-center text-gray-900">${contactInfo.name}</h3>
                    <p class="text-sm text-gray-500 text-center">Conectar con este contacto</p>
                </div>
                <div class="p-4 space-y-3">
                    ${actions.map(action => `
                        <button onclick="executeAction('${action.title}')" 
                                class="w-full flex items-center gap-4 p-3 bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors duration-200">
                            <span class="text-2xl">${action.icon}</span>
                            <div class="flex-1 text-left">
                                <div class="font-medium text-gray-900">${action.title}</div>
                                <div class="text-sm text-gray-500">${action.subtitle}</div>
                            </div>
                        </button>
                    `).join('')}
                </div>
                <div class="p-4 border-t border-gray-200">
                    <button onclick="closeMobileActionSheet()" 
                            class="w-full py-3 text-gray-500 font-medium">
                        Cancelar
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        document.body.style.overflow = 'hidden';
        
        // Store actions globally
        window.mobileActions = actions;
        
        // Close on backdrop click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeMobileActionSheet();
            }
        });
    }

    // Execute mobile action
    window.executeAction = function(actionTitle) {
        const action = window.mobileActions?.find(a => a.title === actionTitle);
        if (action) {
            action.action();
            if (actionTitle !== 'Descargar vCard') {
                closeMobileActionSheet();
            }
        }
    };

    // Close mobile action sheet
    window.closeMobileActionSheet = function() {
        const modal = document.querySelector('.fixed.inset-0.bg-black.bg-opacity-50');
        if (modal) {
            modal.remove();
            document.body.style.overflow = '';
        }
    };

    // Copy contact info to clipboard
    async function copyContactInfo() {
        try {
            let contactText = `📇 ${contactInfo.name}\n`;
            if (contactInfo.title) contactText += `💼 ${contactInfo.title}\n`;
            if (contactInfo.phone) contactText += `📞 ${contactInfo.phone}\n`;
            if (contactInfo.email) contactText += `📧 ${contactInfo.email}\n`;
            if (contactInfo.website) contactText += `🌐 ${contactInfo.website}\n`;
            if (contactInfo.note) contactText += `📝 ${contactInfo.note}\n`;
            
            await navigator.clipboard.writeText(contactText);
            showToast('Información copiada al portapapeles');
        } catch (error) {
            console.error('Error copying to clipboard:', error);
            showToast('Error al copiar información');
        }
    }

    // Enhanced vCard download with better compatibility
    async function downloadEnhancedVCard() {
        try {
            const vcard = generateVCard(contactInfo);
            const blob = new Blob([vcard], { 
                type: 'text/vcard;charset=utf-8' 
            });
            
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `${sanitizeFileName(contactInfo.name)}.vcf`;
            
            // For mobile browsers, try to trigger the native action
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            // Clean up
            setTimeout(() => URL.revokeObjectURL(url), 100);
            
            return true;
        } catch (error) {
            console.error('vCard download failed:', error);
            return false;
        }
    }

    // Generate vCard with enhanced compatibility
    function generateVCard(contact) {
        let vcard = "BEGIN:VCARD\n";
        vcard += "VERSION:3.0\n";
        vcard += `FN:${contact.name}\n`;
        vcard += `N:${contact.name};;;;\n`;
        
        if (contact.email) {
            vcard += `EMAIL;TYPE=INTERNET,HOME:${contact.email}\n`;
        }
        
        if (contact.phone) {
            vcard += `TEL;TYPE=CELL:${contact.phone}\n`;
        }
        
        if (contact.website) {
            vcard += `URL:${contact.website}\n`;
        }
        
        if (contact.title) {
            vcard += `TITLE:${contact.title}\n`;
        }
        
        if (contact.note) {
            vcard += `NOTE:${contact.note}\n`;
        }
        
        // Add current timestamp
        const now = new Date();
        const timestamp = now.toISOString().replace(/[-:]/g, '').split('.')[0] + 'Z';
        vcard += `REV:${timestamp}\n`;
        vcard += "END:VCARD";
        
        return vcard;
    }

    // Sanitize filename for downloads
    function sanitizeFileName(name) {
        return name.replace(/[^a-z0-9áéíóúñü\s-]/gi, '').replace(/\s+/g, '_');
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

    // Show toast notification
    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white px-4 py-2 rounded-lg z-50 animate-fade-in';
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
</script>

<style>
    @keyframes slide-up {
        0% { transform: translateY(100%); opacity: 0; }
        100% { transform: translateY(0); opacity: 1; }
    }
    
    .animate-slide-up {
        animation: slide-up 0.3s ease-out;
    }
    
    @keyframes fade-in-up {
        0% { opacity: 0; transform: translateY(10px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fade-in-up {
        animation: fade-in-up 0.6s ease-out;
    }
</style>