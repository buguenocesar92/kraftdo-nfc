{{-- Enhanced Profile Action Buttons Component --}}
@props([
    'contentProfile' => null,
    'token' => null,
    'colors' => ['primary' => '#3B82F6', 'secondary' => '#8B5CF6', 'accent' => '#EC4899']
])

<div class="space-y-3 sm:space-y-4 animate-fade-in-up" style="animation-delay: 0.6s">
    {{-- Primary Action Button - Save Contact --}}
    <button onclick="downloadVCard()" 
            id="saveContactBtn"
            class="w-full text-white px-6 py-3 sm:py-4 rounded-xl font-semibold hover:shadow-lg transform hover:scale-[1.02] transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 group relative overflow-hidden"
            style="background: linear-gradient(135deg, {{ $colors['primary'] }}, {{ $colors['secondary'] }}, {{ $colors['accent'] }}); focus-ring-color: {{ $colors['primary'] }};">
        <span class="relative z-10 flex items-center justify-center gap-3">
            <div class="w-5 h-5 transition-transform duration-200 group-hover:scale-110">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <span class="text-sm sm:text-base">Guardar Contacto</span>
        </span>
        <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-600"></div>
    </button>

    {{-- Secondary Actions --}}
    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
        {{-- Share Profile Button --}}
        <button onclick="shareProfile()" 
                id="shareProfileBtn"
                class="flex-1 bg-gray-50 hover:bg-gray-100 text-gray-700 px-4 py-3 rounded-xl font-medium border border-gray-200 hover:border-gray-300 transition-all duration-300 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 group">
            <span class="flex items-center justify-center gap-2">
                <div class="w-4 h-4 transition-transform duration-200 group-hover:scale-110">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                    </svg>
                </div>
                <span class="text-sm">Compartir</span>
            </span>
        </button>

        {{-- QR Code Button --}}
        <button onclick="showQRCode()" 
                id="qrCodeBtn"
                class="flex-1 bg-gray-50 hover:bg-gray-100 text-gray-700 px-4 py-3 rounded-xl font-medium border border-gray-200 hover:border-gray-300 transition-all duration-300 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 group">
            <span class="flex items-center justify-center gap-2">
                <div class="w-4 h-4 transition-transform duration-200 group-hover:scale-110">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V4a1 1 0 00-1-1H5a1 1 0 00-1 1v3a1 1 0 001 1zm12 0h2a1 1 0 001-1V4a1 1 0 00-1-1h-2a1 1 0 00-1 1v3a1 1 0 001 1zM5 20h2a1 1 0 001-1v-3a1 1 0 00-1-1H5a1 1 0 00-1 1v3a1 1 0 001 1z"/>
                    </svg>
                </div>
                <span class="text-sm">QR</span>
            </span>
        </button>
    </div>
</div>

{{-- QR Code Modal --}}
<div id="qrModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 max-w-sm w-full animate-scale-in">
        <div class="text-center">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Código QR</h3>
            <div id="qrCode" class="flex justify-center mb-4 bg-white p-4 rounded-xl border-2 border-gray-100"></div>
            <p class="text-sm text-gray-600 mb-4">Escanea para compartir este perfil</p>
            <button onclick="closeQRModal()" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded-xl transition-colors duration-200">
                Cerrar
            </button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode/1.5.3/qrcode.min.js"></script>
<script>
    // Enhanced vCard download with loading state
    async function downloadVCard() {
        const btn = document.getElementById('saveContactBtn');
        const originalContent = btn.innerHTML;
        
        // Show loading state
        btn.innerHTML = `
            <span class="relative z-10 flex items-center justify-center gap-3">
                <div class="w-5 h-5 animate-spin">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <span class="text-sm sm:text-base">Generando...</span>
            </span>
        `;
        btn.disabled = true;
        
        try {
            // Generate enhanced vCard content
            let vcard = "BEGIN:VCARD\nVERSION:3.0\n";
            vcard += "FN:{{ $contentProfile->name ?? $token->name ?? 'Contacto' }}\n";
            
            @if($contentProfile)
                @if($contentProfile->contact_email)
                    vcard += "EMAIL:{{ $contentProfile->contact_email }}\n";
                @endif
                @if($contentProfile->contact_phone)
                    vcard += "TEL:{{ $contentProfile->contact_phone }}\n";
                @endif
                @if($contentProfile->contact_website)
                    vcard += "URL:{{ $contentProfile->contact_website }}\n";
                @endif
                @if($contentProfile->job_title)
                    vcard += "TITLE:{{ $contentProfile->job_title }}\n";
                @endif
                @if($contentProfile->bio)
                    vcard += "NOTE:{{ str_replace(["\n", "\r"], ["\\n", ""], $contentProfile->bio) }}\n";
                @endif
            @endif
            
            vcard += "REV:" + new Date().toISOString().replace(/[-:]/g, '').split('.')[0] + "Z\n";
            vcard += "END:VCARD";
            
            // Create download link
            const element = document.createElement('a');
            const file = new Blob([vcard], {type: 'text/vcard'});
            element.href = URL.createObjectURL(file);
            element.download = "{{ Str::slug($contentProfile->name ?? $token->name ?? 'contacto') }}.vcf";
            document.body.appendChild(element);
            element.click();
            document.body.removeChild(element);
            
            // Show success state briefly
            btn.innerHTML = `
                <span class="relative z-10 flex items-center justify-center gap-3">
                    <div class="w-5 h-5 text-green-300">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="text-sm sm:text-base">¡Guardado!</span>
                </span>
            `;
            
            setTimeout(() => {
                btn.innerHTML = originalContent;
                btn.disabled = false;
            }, 2000);
            
        } catch (error) {
            console.error('Error generating vCard:', error);
            btn.innerHTML = originalContent;
            btn.disabled = false;
        }
    }

    // Share profile functionality
    async function shareProfile() {
        const profileData = {
            title: '{{ $contentProfile->name ?? $token->name ?? "Perfil Digital" }}',
            text: '{{ $contentProfile->bio ?? "Conecta conmigo a través de mi perfil digital" }}',
            url: window.location.href
        };

        if (navigator.share && navigator.canShare && navigator.canShare(profileData)) {
            try {
                await navigator.share(profileData);
            } catch (error) {
                if (error.name !== 'AbortError') {
                    fallbackShare();
                }
            }
        } else {
            fallbackShare();
        }
    }

    // Fallback share functionality
    function fallbackShare() {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(window.location.href).then(() => {
                showToast('Enlace copiado al portapapeles');
            }).catch(() => {
                showShareModal();
            });
        } else {
            showShareModal();
        }
    }

    // Show QR Code modal
    function showQRCode() {
        const modal = document.getElementById('qrModal');
        const qrContainer = document.getElementById('qrCode');
        
        // Clear previous QR code
        qrContainer.innerHTML = '';
        
        // Generate QR code
        QRCode.toCanvas(qrContainer, window.location.href, {
            width: 200,
            height: 200,
            colorDark: '#1f2937',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.M
        }, function(error) {
            if (error) {
                qrContainer.innerHTML = '<p class="text-red-500">Error generando código QR</p>';
            }
        });
        
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    // Close QR Code modal
    function closeQRModal() {
        const modal = document.getElementById('qrModal');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Toast notification
    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white px-4 py-2 rounded-lg z-50 animate-fade-in';
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    // Close modal on outside click
    document.getElementById('qrModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeQRModal();
        }
    });

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeQRModal();
        }
    });
</script>

<style>
    @keyframes scale-in {
        0% { transform: scale(0.9); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
    
    .animate-scale-in {
        animation: scale-in 0.2s ease-out;
    }
    
    @keyframes fade-in {
        0% { opacity: 0; transform: translateY(10px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fade-in {
        animation: fade-in 0.3s ease-out;
    }
</style>