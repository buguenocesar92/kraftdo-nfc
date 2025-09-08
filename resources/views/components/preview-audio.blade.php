{{-- Preview Audio Component --}}
@props([
    'audioUrl'
])

@if($audioUrl)
    <audio id="background-audio" preload="auto" loop src="{{ $audioUrl }}">
        <source src="{{ $audioUrl }}" type="audio/mpeg">
        <source src="{{ $audioUrl }}" type="audio/wav">
        <source src="{{ $audioUrl }}" type="audio/mp4">
        Tu navegador no soporta el elemento de audio.
    </audio>
    <!-- Audio control handled by content-preview.js -->
@endif