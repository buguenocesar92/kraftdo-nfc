<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 kraftdo-gradient border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:kraftdo-gradient-reverse hover:scale-105 focus:kraftdo-gradient-reverse active:kraftdo-gradient focus:outline-none focus:ring-2 focus:ring-kraftdo-green focus:ring-offset-2 focus:ring-offset-kraftdo-dark transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
