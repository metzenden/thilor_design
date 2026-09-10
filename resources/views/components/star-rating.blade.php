@props(['rating' => 0, 'count' => null])

<div class="flex items-center gap-1 text-gold">
    @for ($i = 1; $i <= 5; $i++)
        @if ($i <= round($rating))
            <x-heroicon-s-star class="h-4 w-4" />
        @else
            <x-heroicon-o-star class="h-4 w-4" />
        @endif
    @endfor
    @if(!is_null($count))
        <span class="ml-1 text-xs text-ink/50">({{ $count }} avis)</span>
    @endif
</div>
