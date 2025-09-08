@extends('layouts.gift')

@section('title', __('gift.title', ['title' => $content->title]))
@section('description', $content->description)

@section('body-class', $defaultBgClass ?? 'bg-gradient-to-br from-pink-50 via-purple-50 to-indigo-100')
@section('font-class', $fontFamily ?? 'font-sans')  
@section('body-style', $customBg ?? '')

@section('background-decorations')
    @include('nfc.partials.theme-decorations', ['theme' => $theme ?? []])
@endsection

@section('header')
    <x-gift-header 
        :content="$content"
        :theme="$theme ?? []"
        :current-subtype="$currentSubtype ?? []"
        :config="$config ?? []"
    />
@endsection

@section('message')
    <div class="gift-section">
        @include('nfc.partials.message-card', [
            'content' => $content, 
            'theme' => $theme ?? [], 
            'fontFamily' => $fontFamily ?? 'font-sans'
        ])
    </div>
@endsection

@section('sender-info')
    <x-gift-sender-info 
        :content="$content"
        :theme="$theme ?? []"
    />
@endsection

@section('main-image')
    <x-gift-main-image 
        :image-url="$content->image_url"
        :title="$content->title"
    />
@endsection

@section('multimedia')
    <x-gift-multimedia-section 
        :multimedia="$multimedia ?? []"
        :theme="$theme ?? []"
    />
@endsection

@section('footer')
    @if(!isset($hideFooter) || !$hideFooter)
        <div class="gift-footer-wrapper">
            <x-shared.footer 
                :content="$content" 
                theme="profile" 
                :show-admin-info="request()->has('admin')" 
            />
        </div>
    @endif
@endsection

@section('modals')
    <!-- Gallery Modal -->
    @include('nfc.partials.gallery-modal', ['gallery' => ($multimedia['gallery'] ?? [])])
@endsection

@section('scripts')
    <x-gift-scripts-data :js-config="$jsConfig ?? []" />
@endsection