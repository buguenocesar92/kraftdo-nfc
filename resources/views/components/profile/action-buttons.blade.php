{{-- Profile Action Buttons Component --}}
@props([
    'contentProfile' => null,
    'token' => null
])

<div class="text-center">
    <button onclick="downloadVCard()" 
            class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg transform hover:scale-105 transition-all duration-200">
        💾 Guardar Contacto
    </button>
</div>

<script>
    function downloadVCard() {
        // Generate vCard content
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
            @if($contentProfile->bio)
                vcard += "NOTE:{{ str_replace("\n", "\\n", $contentProfile->bio) }}\n";
            @endif
        @endif
        
        vcard += "END:VCARD";
        
        // Create download link
        const element = document.createElement('a');
        const file = new Blob([vcard], {type: 'text/vcard'});
        element.href = URL.createObjectURL(file);
        element.download = "{{ Str::slug($contentProfile->name ?? $token->name ?? 'contacto') }}.vcf";
        document.body.appendChild(element);
        element.click();
        document.body.removeChild(element);
    }
</script>