{{-- Spotify Player Component --}}
@if(preg_match('/spotify\.com\/track\/([a-zA-Z0-9]+)/', $audio['url'] ?? '', $matches))
    <div class="rounded-2xl overflow-hidden shadow-lg">
        <iframe src="https://open.spotify.com/embed/track/{{ $matches[1] }}" 
                width="100%" 
                height="152" 
                frameborder="0" 
                allowtransparency="true" 
                allow="encrypted-media"
                class="rounded-2xl">
        </iframe>
    </div>
@endif