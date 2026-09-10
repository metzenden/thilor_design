<x-layouts.shop :title="$collection->name.' — THILOR DESIGN'" :metaDescription="$collection->description">
    <div class="container-shop py-12">
        <nav class="mb-4 text-xs text-ink/50">
            <a href="{{ route('collections.index') }}" class="hover:text-gold">Collections</a>
            <span class="mx-1">/</span>
            <span class="text-ink">{{ $collection->name }}</span>
        </nav>
        <h1 class="section-title">{{ $collection->name }}</h1>
        <p class="mt-2 max-w-2xl text-sm text-ink/60">{{ $collection->description }}</p>

        <div class="mt-8 grid grid-cols-2 gap-6 sm:grid-cols-3 md:grid-cols-4">
            @foreach($products as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
        <div class="mt-8">{{ $products->links() }}</div>
    </div>
</x-layouts.shop>
