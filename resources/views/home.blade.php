<x-layouts.shop :title="\App\Models\Setting::get('seo_default_title')">
    {{-- Hero --}}
    @if($heroBanners->isNotEmpty())
        @php($hero = $heroBanners->first())
        <section class="relative overflow-hidden">
            <img src="{{ $hero->image_url }}" alt="{{ $hero->title }}" class="h-[420px] w-full object-cover md:h-[520px]">
            <div class="absolute inset-0 bg-gradient-to-r from-ink/70 via-ink/30 to-transparent"></div>
            <div class="container-shop absolute inset-0 flex flex-col justify-center gap-4">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-gold">{{ \App\Models\Setting::get('shop_tagline') }}</p>
                <h1 class="max-w-xl font-serif text-4xl font-semibold leading-tight text-white md:text-6xl">{{ $hero->title }}</h1>
                <p class="max-w-md text-white/85">{{ $hero->subtitle }}</p>
                <a href="{{ $hero->link ?? route('catalog.index') }}" class="btn-gold mt-2 w-fit">
                    {{ $hero->cta_label ?? 'Découvrir la collection' }}
                </a>
            </div>
        </section>
    @endif

    {{-- Réassurance --}}
    <section class="border-b border-ink/10 bg-white">
        <div class="container-shop grid grid-cols-2 gap-6 py-6 text-center text-sm text-ink/70 md:grid-cols-4">
            <div>
                <x-heroicon-o-truck class="mx-auto h-6 w-6 text-gold" />
                <p class="mt-2 font-medium">Livraison rapide</p>
                <p class="text-xs text-ink/50">Partout au Sénégal</p>
            </div>
            <div>
                <x-heroicon-o-shield-check class="mx-auto h-6 w-6 text-gold" />
                <p class="mt-2 font-medium">Paiement sécurisé</p>
                <p class="text-xs text-ink/50">100% sécurisé</p>
            </div>
            <div>
                <x-heroicon-o-arrow-path class="mx-auto h-6 w-6 text-gold" />
                <p class="mt-2 font-medium">Satisfait ou remboursé</p>
                <p class="text-xs text-ink/50">14 jours pour changer d'avis</p>
            </div>
            <div>
                <x-heroicon-o-chat-bubble-left-right class="mx-auto h-6 w-6 text-gold" />
                <p class="mt-2 font-medium">Assistance 7/7</p>
                <p class="text-xs text-ink/50">Nous sommes à votre écoute</p>
            </div>
        </div>
    </section>

    {{-- Catégories populaires --}}
    <section class="container-shop py-12">
        <div class="mb-6 flex items-end justify-between">
            <h2 class="section-title">Catégories populaires</h2>
            <a href="{{ route('catalog.index') }}" class="text-sm font-semibold text-gold hover:underline">Voir tout &rsaquo;</a>
        </div>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-5">
            @foreach($categories as $category)
                <a href="{{ route('catalog.category', $category) }}" class="group text-center">
                    <div class="aspect-square overflow-hidden rounded-sm bg-white">
                        @if($category->image)
                            <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="h-full w-full object-cover transition group-hover:scale-105">
                        @endif
                    </div>
                    <p class="mt-2 text-xs font-semibold uppercase tracking-wide text-ink/70">{{ $category->name }}</p>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Nouveautés --}}
    @if($newArrivals->isNotEmpty())
        <section class="container-shop py-8">
            <div class="mb-6 flex items-end justify-between">
                <h2 class="section-title">Nouveautés</h2>
                <a href="{{ route('catalog.index', ['sort' => 'newest']) }}" class="text-sm font-semibold text-gold hover:underline">Voir tout &rsaquo;</a>
            </div>
            <div class="grid grid-cols-2 gap-6 sm:grid-cols-3 md:grid-cols-4">
                @foreach($newArrivals as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Promotions --}}
    @if($onSale->isNotEmpty())
        <section class="bg-white py-12">
            <div class="container-shop">
                <div class="mb-6 flex items-end justify-between">
                    <h2 class="section-title">Promotions</h2>
                    <a href="{{ route('catalog.promotions') }}" class="text-sm font-semibold text-gold hover:underline">Voir tout &rsaquo;</a>
                </div>
                <div class="grid grid-cols-2 gap-6 sm:grid-cols-3 md:grid-cols-4">
                    @foreach($onSale as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Meilleures ventes / vedette --}}
    @if($featured->isNotEmpty())
        <section class="container-shop py-12">
            <div class="mb-6 flex items-end justify-between">
                <h2 class="section-title">Meilleures ventes</h2>
            </div>
            <div class="grid grid-cols-2 gap-6 sm:grid-cols-3 md:grid-cols-4">
                @foreach($featured as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Collections --}}
    @if($collections->isNotEmpty())
        <section class="bg-white py-12">
            <div class="container-shop">
                <h2 class="section-title mb-6">Collections</h2>
                <div class="grid gap-4 md:grid-cols-3">
                    @foreach($collections as $collection)
                        <a href="{{ route('collections.show', $collection) }}" class="group relative block h-56 overflow-hidden rounded-sm">
                            @if($collection->image)
                                <img src="{{ $collection->image_url }}" alt="{{ $collection->name }}" class="h-full w-full object-cover transition group-hover:scale-105">
                            @endif
                            <div class="absolute inset-0 bg-ink/40"></div>
                            <p class="absolute bottom-4 left-4 font-serif text-xl font-semibold text-white">{{ $collection->name }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Avis --}}
    @if($reviews->isNotEmpty())
        <section class="container-shop py-12">
            <h2 class="section-title mb-6">Ce que nos clientes en disent</h2>
            <div class="grid gap-6 md:grid-cols-3">
                @foreach($reviews as $review)
                    <div class="rounded-sm bg-white p-5 shadow-sm">
                        <x-star-rating :rating="$review->rating" />
                        <p class="mt-3 text-sm text-ink/70">&laquo; {{ $review->comment }} &raquo;</p>
                        <p class="mt-3 text-xs font-semibold text-ink">{{ $review->user->name }}</p>
                        <p class="text-xs text-ink/50">{{ $review->product->name }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Newsletter --}}
    <section class="bg-bordeaux py-12 text-center text-white">
        <div class="container-shop">
            <h2 class="font-serif text-2xl font-semibold">Restez informée de nos nouveautés</h2>
            <p class="mt-2 text-white/80">Inscrivez-vous et recevez nos offres exclusives.</p>
            <form action="{{ route('newsletter.store') }}" method="POST" class="mx-auto mt-5 flex max-w-md gap-2">
                @csrf
                <input type="email" name="email" required placeholder="Votre email" class="w-full rounded-sm border-0 text-sm text-ink">
                <button type="submit" class="btn-gold shrink-0">S'inscrire</button>
            </form>
        </div>
    </section>
</x-layouts.shop>
