@props(['class' => 'size-8', 'sidebar' => false])

<a {{ $attributes->merge(['class' => 'flex items-center gap-2 px-1']) }}>
    <img src="{{ asset('images/LogoSMKN7BE.png') }}" alt="Logo" class="{{ $class }} object-contain">
    
    <span class="font-semibold text-zinc-900 dark:text-white mb-0.5">
        {{ config('app.name', 'Inventory System') }}
    </span>
</a>