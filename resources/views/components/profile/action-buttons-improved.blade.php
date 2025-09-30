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
                const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
                const isAndroid = /Android/.test(navigator.userAgent);
                
                // Method 1: Web Share API with files (iOS/Android modern)
                if (navigator.share && navigator.canShare) {
                    const file = new File([vcard], `${contactInfo.name.replace(/[^a-z0-9]/gi, '_')}.vcf`, {
                        type: 'text/vcard'
                    });
                    
                    if (navigator.canShare({ files: [file] })) {
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
                            // Fallback to next method
                            this.tryNativeMethods(vcard, btn, originalContent, isIOS, isAndroid);
                        });
                        return;
                    }
                }
                
                // If Web Share API fails, try native methods
                this.tryNativeMethods(vcard, btn, originalContent, isIOS, isAndroid);
            },

            tryNativeMethods(vcard, btn, originalContent, isIOS, isAndroid) {
                try {
                    if (isIOS) {
                        // Method 2: iOS - Try data URL first
                        this.tryDataURL(vcard, btn, originalContent);
                    } else if (isAndroid) {
                        // Method 3: Try multiple Android methods
                        if (!this.tryAndroidIntent(btn, originalContent)) {
                            if (!this.tryAndroidIntent2(btn, originalContent)) {
                                this.tryDataURL(vcard, btn, originalContent);
                            }
                        }
                    } else {
                        // Method 4: Desktop/other - Data URL
                        this.tryDataURL(vcard, btn, originalContent);
                    }
                } catch (error) {
                    // Final fallback: Web Share as text or download
                    this.finalFallback(vcard, btn, originalContent);
                }
            },

            tryDataURL(vcard, btn, originalContent) {
                try {
                    // Data URL - should trigger native handler
                    const dataURL = `data:text/vcard;charset=utf-8,${encodeURIComponent(vcard)}`;
                    window.location.href = dataURL;
                    
                    setButtonState(btn, 'success', '¡Contacto guardado!');
                    setTimeout(() => setButtonState(btn, 'default', originalContent), 2500);
                    return true;
                } catch (error) {
                    return false;
                }
            },

            tryAndroidIntent(btn, originalContent) {
                try {
                    const contact = contactInfo;
                    // Android Intent format 1 - Modern format
                    let intentUrl = "intent:#Intent;action=android.intent.action.INSERT;type=vnd.android.cursor.dir/contact;";
                    
                    if (contact.name) {
                        intentUrl += `S.android.intent.extra.INSERT_NAME=${encodeURIComponent(contact.name)};`;
                    }
                    if (contact.phone) {
                        intentUrl += `S.android.intent.extra.INSERT_PHONE=${encodeURIComponent(contact.phone)};`;
                    }
                    if (contact.email) {
                        intentUrl += `S.android.intent.extra.INSERT_EMAIL=${encodeURIComponent(contact.email)};`;
                    }
                    if (contact.title) {
                        intentUrl += `S.android.intent.extra.INSERT_COMPANY=${encodeURIComponent(contact.title)};`;
                    }
                    
                    intentUrl += "package=com.android.contacts;end";
                    
                    window.location.href = intentUrl;
                    
                    setButtonState(btn, 'success', '¡Abriendo Contactos!');
                    setTimeout(() => setButtonState(btn, 'default', originalContent), 2500);
                    return true;
                } catch (error) {
                    return false;
                }
            },

            tryAndroidIntent2(btn, originalContent) {
                try {
                    const contact = contactInfo;
                    // Android Intent format 2 - Alternative format
                    let intentUrl = "intent://add/#Intent;action=android.intent.action.INSERT;type=vnd.android.cursor.dir/contact;";
                    
                    if (contact.name) {
                        intentUrl += `S.name=${encodeURIComponent(contact.name)};`;
                    }
                    if (contact.phone) {
                        intentUrl += `S.phone=${encodeURIComponent(contact.phone)};`;
                    }
                    if (contact.email) {
                        intentUrl += `S.email=${encodeURIComponent(contact.email)};`;
                    }
                    if (contact.title) {
                        intentUrl += `S.company=${encodeURIComponent(contact.title)};`;
                    }
                    
                    intentUrl += "end";
                    
                    window.location.href = intentUrl;
                    
                    setButtonState(btn, 'success', '¡Abriendo Contactos!');
                    setTimeout(() => setButtonState(btn, 'default', originalContent), 2500);
                    return true;
                } catch (error) {
                    return false;
                }
            },

            finalFallback(vcard, btn, originalContent) {
                // Try Web Share as text first
                if (navigator.share) {
                    navigator.share({
                        title: `Contacto: ${contactInfo.name}`,
                        text: vcard
                    }).then(() => {
                        setButtonState(btn, 'success', '¡Contacto compartido!');
                        setTimeout(() => setButtonState(btn, 'default', originalContent), 2500);
                    }).catch(() => {
                        // Last resort: download
                        this.downloadVCard(vcard, btn, originalContent);
                    });
                } else {
                    // Last resort: download
                    this.downloadVCard(vcard, btn, originalContent);
                }
            },

            downloadVCard(vcard, btn, originalContent) {
                try {
                    const blob = new Blob([vcard], { type: 'text/vcard;charset=utf-8' });
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    
                    link.href = url;
                    link.download = `${contactInfo.name.replace(/[^a-z0-9]/gi, '_')}.vcf`;
                    link.style.display = 'none';
                    
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    
                    setTimeout(() => URL.revokeObjectURL(url), 100);
                    
                    setButtonState(btn, 'success', '¡Descargado!');
                    setTimeout(() => setButtonState(btn, 'default', originalContent), 2500);
                } catch (error) {
                    setButtonState(btn, 'error', 'Error al guardar');
                    setTimeout(() => setButtonState(btn, 'default', originalContent), 3000);
                }
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
</style>