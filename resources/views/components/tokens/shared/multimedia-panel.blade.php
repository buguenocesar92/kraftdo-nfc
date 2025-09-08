@props(['token', 'content'])

<!-- Panel de Multimedia (Para tipos no-PROFILE) -->
<div id="multimedia-panel" class="mt-6 p-4 bg-kraftdo-lime/10 rounded-2xl border border-kraftdo-green/20">
    
    <!-- Tabs de multimedia basadas en el plan -->
    <div class="border-b border-kraftdo-green/20 mb-4">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <!-- Audio siempre disponible (música de fondo) -->
            <button type="button" class="multimedia-tab-btn py-2 px-1 border-b-2 border-kraftdo-green text-kraftdo-blue whitespace-nowrap font-medium" data-tab="audio">
                🎵 Audio
            </button>
            
            <button type="button" class="multimedia-tab-btn py-2 px-1 border-b-2 border-transparent text-kraftdo-navy/70 hover:text-kraftdo-blue hover:border-kraftdo-green/50 whitespace-nowrap" data-tab="video">
                🎥 Video
            </button>
            
            <button type="button" class="multimedia-tab-btn py-2 px-1 border-b-2 border-transparent text-kraftdo-navy/70 hover:text-kraftdo-blue hover:border-kraftdo-green/50 whitespace-nowrap" data-tab="gallery">
                📸 Galería
            </button>
        </nav>
    </div>

    <!-- Audio Tab -->
    <div id="audio-tab" class="multimedia-tab-content">
        <x-tokens.shared.audio-upload :token="$token" :content="$content" />
    </div>

    <!-- Video Tab -->
    <div id="video-tab" class="multimedia-tab-content hidden">
        <x-tokens.shared.video-upload :token="$token" :content="$content" />
    </div>

    <!-- Gallery Tab -->
    <div id="gallery-tab" class="multimedia-tab-content hidden">
        <x-tokens.shared.gallery-upload :token="$token" :content="$content" />
    </div>
</div>

@push('scripts')
<script>
// JavaScript para multimedia tabs
document.querySelectorAll('.multimedia-tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const tabName = this.dataset.tab;
        
        // Actualizar botones
        document.querySelectorAll('.multimedia-tab-btn').forEach(b => {
            b.classList.remove('border-kraftdo-green', 'text-kraftdo-blue');
            b.classList.add('border-transparent', 'text-kraftdo-navy/70');
        });
        this.classList.remove('border-transparent', 'text-kraftdo-navy/70');
        this.classList.add('border-kraftdo-green', 'text-kraftdo-blue');
        
        // Mostrar/ocultar contenido
        document.querySelectorAll('.multimedia-tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        document.getElementById(tabName + '-tab').classList.remove('hidden');
    });
});
</script>
@endpush 