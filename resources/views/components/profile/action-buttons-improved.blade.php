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
                
                // Method 1: Direct vCard download (like TukCards.cl)
                // This is the most reliable method across all devices
                this.downloadVCardDirect(btn, originalContent);
            },

            downloadVCardDirect(btn, originalContent) {
                try {
                    const vcard = this.generateVCard();
                    
                    // Create blob with proper MIME type
                    const blob = new Blob([vcard], { 
                        type: 'text/vcard;charset=utf-8' 
                    });
                    
                    // Create download URL
                    const url = URL.createObjectURL(blob);
                    
                    // Create invisible link and trigger download
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = `${contactInfo.name.replace(/[^a-z0-9]/gi, '_')}.vcf`;
                    link.style.display = 'none';
                    
                    // Add to DOM, click, and remove
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    
                    // Clean up object URL
                    setTimeout(() => URL.revokeObjectURL(url), 100);
                    
                    setButtonState(btn, 'success', '¡Contacto descargado!');
                    setTimeout(() => setButtonState(btn, 'default', originalContent), 2500);
                    
                } catch (error) {
                    console.error('Error downloading vCard:', error);
                    setButtonState(btn, 'error', 'Error al descargar');
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