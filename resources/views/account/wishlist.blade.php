<x-layouts.shop title="Mes favoris — THILOR DESIGN">
    <div class="container-shop py-8">
        <h1 class="mb-6 font-serif text-2xl font-semibold">Mon compte</h1>
        <div class="flex flex-col gap-6 md:flex-row">
            <x-account.sidebar active="wishlist" />

            <div class="flex-1">
                <div class="rounded-sm bg-white p-6">
                    <h2 class="mb-4 font-serif text-lg font-semibold">Mes favoris</h2>

                    @if($products->isEmpty())
                        <p class="text-sm text-ink/50">Vous n'avez pas encore de favoris.</p>
                    @else
                        <div class="grid grid-cols-2 gap-6 sm:grid-cols-3">
                            @foreach($products as $wishlist)
                                <x-product-card :product="$wishlist->product" />
                            @endforeach
                        </div>
                        <div class="mt-6">{{ $products->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.shop>
