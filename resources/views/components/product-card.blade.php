@props(['product'])

@php
    $image = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
    $inStock = $product->variants->isEmpty() || $product->variants->sum('stock') > 0;
@endphp

<div class="group relative">
    <a href="{{ route('products.show', $product) }}" class="block">
        <div class="relative aspect-[4/5] overflow-hidden rounded-sm bg-white">
            @if($image)
                <img
                    src="{{ $image->url }}"
                    alt="{{ $image->alt_text ?? $product->name }}"
                    loading="lazy"
                    class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                >
            @endif

            @if($product->discount_percent)
                <span class="absolute left-2 top-2 rounded-sm bg-bordeaux px-2 py-1 text-xs font-semibold text-white">
                    -{{ $product->discount_percent }}%
                </span>
            @endif

            @unless($inStock)
                <span class="absolute inset-x-0 bottom-0 bg-ink/80 py-1 text-center text-xs font-semibold uppercase text-white">
                    Rupture de stock
                </span>
            @endunless

            @auth
                <button
                    type="submit"
                    form="wishlist-toggle-{{ $product->id }}"
                    class="absolute right-2 top-2 flex h-8 w-8 items-center justify-center rounded-full bg-white/90 text-ink/60 hover:text-bordeaux"
                    title="Ajouter aux favoris"
                >
                    <x-heroicon-o-heart class="h-4 w-4" />
                </button>
            @endauth
        </div>

        <div class="mt-3">
            <p class="truncate text-sm font-medium text-ink">{{ $product->name }}</p>
            <div class="mt-1 flex items-center gap-2 text-sm">
                <span class="font-semibold text-ink">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                @if($product->compare_at_price && $product->compare_at_price > $product->price)
                    <span class="text-ink/40 line-through">{{ number_format($product->compare_at_price, 0, ',', ' ') }} FCFA</span>
                @endif
            </div>
        </div>
    </a>

    @auth
        <form id="wishlist-toggle-{{ $product->id }}" action="{{ route('wishlist.toggle', $product) }}" method="POST" class="hidden">
            @csrf
        </form>
    @endauth
</div>
