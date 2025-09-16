{{-- Dashboard Messages Component --}}
@if(session('success'))
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-700 px-6 py-4 rounded-2xl mb-8 animate-fade-in-up">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 mr-3"></i>
            {{ session('success') }}
        </div>
    </div>
@endif

@if(session('error'))
    <div class="bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl mb-8 animate-fade-in-up">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
            {{ session('error') }}
        </div>
    </div>
@endif