@props(['current'])

@php
    $steps = [
        'informations' => 'Informations',
        'livraison' => 'Livraison',
        'paiement' => 'Paiement',
        'confirmation' => 'Confirmation',
    ];
    $order = array_keys($steps);
    $currentIndex = array_search($current, $order);
@endphp

<div class="mb-8 flex flex-wrap items-center gap-2 text-sm">
    @foreach($steps as $key => $label)
        @php($index = array_search($key, $order))
        <span class="flex items-center gap-2 {{ $index <= $currentIndex ? 'font-semibold text-gold' : 'text-ink/40' }}">
            <span class="flex h-5 w-5 items-center justify-center rounded-full text-xs {{ $index < $currentIndex ? 'bg-gold text-white' : ($index === $currentIndex ? 'border-2 border-gold' : 'border border-ink/20') }}">
                @if($index < $currentIndex)
                    <x-heroicon-s-check class="h-3 w-3" />
                @else
                    {{ $index + 1 }}
                @endif
            </span>
            {{ $label }}
        </span>
        @if(!$loop->last)
            <span class="text-ink/20">—</span>
        @endif
    @endforeach
</div>
