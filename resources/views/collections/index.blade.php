<x-layouts.shop title="Collections — THILOR DESIGN">
    <div class="container-shop py-12">
        <h1 class="section-title mb-8">Nos collections</h1>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($collections as $collection)
                <a href="{{ route('collections.show', $collection) }}" class="group relative block h-64 overflow-hidden rounded-sm">
                    @if($collection->image)
                        <img src="{{ $collection->image_url }}" alt="{{ $collection->name }}" class="h-full w-full object-cover transition group-hover:scale-105">
                    @endif
                    <div class="absolute inset-0 bg-ink/40"></div>
                    <div class="absolute bottom-4 left-4 text-white">
                        <p class="font-serif text-xl font-semibold">{{ $collection->name }}</p>
                        <p class="text-xs text-white/80">{{ $collection->products_count }} produits</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</x-layouts.shop>
