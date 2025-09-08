<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-transparent border-2 border-kraftdo-green rounded-md font-semibold text-xs text-kraftdo-green uppercase tracking-widest shadow-sm hover:bg-kraftdo-green hover:text-kraftdo-dark focus:outline-none focus:ring-2 focus:ring-kraftdo-green focus:ring-offset-2 focus:ring-offset-kraftdo-dark disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
