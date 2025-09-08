@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-kraftdo-navy/50 border-kraftdo-navy text-white placeholder-gray-400 focus:border-kraftdo-green focus:ring-kraftdo-green rounded-md shadow-sm']) }}>
