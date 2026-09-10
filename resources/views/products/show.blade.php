<x-layouts.shop :title="($product->meta_title ?: $product->name).' — THILOR DESIGN'" :metaDescription="$product->meta_description ?: $product->short_description" :canonical="route('products.show', $product)">
    @push('head')
        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => $product->short_description,
            'sku' => $product->sku,
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => 'XOF',
                'price' => $product->price,
                'availability' => $product->is_in_stock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            ],
        ]) !!}
        </script>
    @endpush

    <div class="container-shop py-8">
        <nav class="mb-4 text-xs text-ink/50">
            <a href="{{ route('home') }}" class="hover:text-gold">Accueil</a>
            <span class="mx-1">/</span>
            <a href="{{ route('catalog.category', $product->category) }}" class="hover:text-gold">{{ $product->category->name }}</a>
            <span class="mx-1">/</span>
            <span class="text-ink">{{ $product->name }}</span>
        </nav>

        <div class="grid gap-10 md:grid-cols-2">
            {{-- Galerie --}}
            <div x-data="{ active: 0 }">
                <div class="aspect-[4/5] overflow-hidden rounded-sm bg-white">
                    @foreach($product->images as $image)
                        <img x-show="active === {{ $loop->index }}" src="{{ $image->url }}" alt="{{ $image->alt_text ?? $product->name }}" class="h-full w-full object-cover">
                    @endforeach
                </div>
                @if($product->images->count() > 1)
                    <div class="mt-3 flex gap-2">
                        @foreach($product->images as $image)
                            <button @click="active = {{ $loop->index }}" class="h-16 w-16 overflow-hidden rounded-sm border-2" :class="active === {{ $loop->index }} ? 'border-gold' : 'border-transparent'">
                                <img src="{{ $image->url }}" alt="" class="h-full w-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Infos --}}
            <div x-data="productForm({
                sizes: {{ $sizes->toJson() }},
                colors: {{ $colors->toJson() }},
                variants: {{ $product->variants->map(fn($v) => ['id' => $v->id, 'size' => $v->size, 'color' => $v->color, 'stock' => $v->stock])->values()->toJson() }}
            })">
                <h1 class="font-serif text-3xl font-semibold text-ink">{{ $product->name }}</h1>

                <div class="mt-2">
                    <x-star-rating :rating="$product->average_rating" :count="$product->approvedReviews()->count()" />
                </div>

                <div class="mt-4 flex items-center gap-3">
                    <span class="text-2xl font-semibold text-ink">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                    @if($product->compare_at_price && $product->compare_at_price > $product->price)
                        <span class="text-lg text-ink/40 line-through">{{ number_format($product->compare_at_price, 0, ',', ' ') }} FCFA</span>
                        <span class="rounded-sm bg-bordeaux px-2 py-0.5 text-xs font-semibold text-white">-{{ $product->discount_percent }}%</span>
                    @endif
                </div>

                <p class="mt-2 text-sm" :class="currentStock() > 0 ? 'text-green-700' : 'text-red-600'">
                    <span x-text="currentStock() > 0 ? 'En stock' : 'Rupture de stock'"></span>
                </p>

                <p class="mt-4 text-sm leading-relaxed text-ink/70">{{ $product->short_description }}</p>

                <form action="{{ route('cart.store') }}" method="POST" class="mt-6 space-y-5">
                    @csrf
                    <input type="hidden" name="variant_id" :value="selectedVariantId()">

                    @if($colors->isNotEmpty())
                        <div>
                            <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-ink/60">Couleur</p>
                            <div class="flex gap-2">
                                @foreach($colors as $color)
                                    <button type="button" @click="color = '{{ $color }}'"
                                        class="rounded-sm border px-3 py-2 text-xs"
                                        :class="color === '{{ $color }}' ? 'border-gold bg-gold-50 text-gold-700' : 'border-ink/20'">
                                        {{ $color }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($sizes->isNotEmpty())
                        <div>
                            <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-ink/60">Taille</p>
                            <div class="flex gap-2">
                                @foreach($sizes as $size)
                                    <button type="button" @click="size = '{{ $size }}'"
                                        class="flex h-9 w-9 items-center justify-center rounded-sm border text-xs"
                                        :class="size === '{{ $size }}' ? 'border-gold bg-gold text-white' : 'border-ink/20'">
                                        {{ $size }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div>
                        <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-ink/60">Quantité</p>
                        <div class="flex w-fit items-center rounded-sm border border-ink/20">
                            <button type="button" @click="qty = Math.max(1, qty - 1)" class="px-3 py-2">-</button>
                            <input type="number" name="quantity" x-model.number="qty" min="1" class="w-14 border-0 text-center focus:ring-0">
                            <button type="button" @click="qty = qty + 1" class="px-3 py-2">+</button>
                        </div>
                    </div>

                    <button type="submit" class="btn-gold w-full" :disabled="currentStock() < 1">
                        <x-heroicon-o-shopping-bag class="h-5 w-5" />
                        Ajouter au panier
                    </button>
                </form>

                <div class="mt-4 flex items-center gap-4 text-sm text-ink/60">
                    @auth
                        <form action="{{ route('wishlist.toggle', $product) }}" method="POST">
                            @csrf
                            <button type="submit" class="flex items-center gap-1 hover:text-bordeaux">
                                <x-heroicon-o-heart class="h-4 w-4" /> Ajouter aux favoris
                            </button>
                        </form>
                    @endauth
                    <button type="button" onclick="navigator.share ? navigator.share({title: document.title, url: window.location.href}) : alert('Copiez le lien : ' + window.location.href)" class="flex items-center gap-1 hover:text-gold">
                        <x-heroicon-o-share class="h-4 w-4" /> Partager
                    </button>
                </div>

                <div class="mt-6 space-y-2 rounded-sm bg-white p-4 text-sm text-ink/70">
                    <p class="flex items-center gap-2"><x-heroicon-o-truck class="h-4 w-4 text-gold" /> Livraison entre 24h et 72h</p>
                    <p class="flex items-center gap-2"><x-heroicon-o-banknotes class="h-4 w-4 text-gold" /> Paiement à la livraison disponible</p>
                    <p class="flex items-center gap-2"><x-heroicon-o-arrow-path class="h-4 w-4 text-gold" /> Satisfait ou remboursé sous 14 jours</p>
                </div>
            </div>
        </div>

        {{-- Onglets --}}
        <div class="mt-12" x-data="{ tab: 'description' }">
            <div class="flex gap-6 border-b border-ink/10 text-sm font-semibold uppercase tracking-wide">
                <button @click="tab = 'description'" :class="tab === 'description' ? 'border-b-2 border-gold text-gold' : 'text-ink/50'" class="pb-3">Description</button>
                <button @click="tab = 'guide'" :class="tab === 'guide' ? 'border-b-2 border-gold text-gold' : 'text-ink/50'" class="pb-3">Guide des tailles</button>
                <button @click="tab = 'livraison'" :class="tab === 'livraison' ? 'border-b-2 border-gold text-gold' : 'text-ink/50'" class="pb-3">Livraison</button>
                <button @click="tab = 'avis'" :class="tab === 'avis' ? 'border-b-2 border-gold text-gold' : 'text-ink/50'" class="pb-3">Avis ({{ $product->approvedReviews()->count() }})</button>
            </div>

            <div x-show="tab === 'description'" class="prose prose-sm mt-6 max-w-none text-ink/70">
                {!! $product->description !!}
                <ul class="mt-4 list-none space-y-1 text-sm">
                    @if($product->material)<li><strong>Tissu :</strong> {{ $product->material }}</li>@endif
                    @if($product->care_instructions)<li><strong>Entretien :</strong> {{ $product->care_instructions }}</li>@endif
                </ul>
            </div>

            <div x-show="tab === 'guide'" x-cloak class="mt-6 text-sm text-ink/70">
                <p>Consultez notre <a href="{{ route('pages.show', 'guide-des-tailles') }}" class="text-gold underline">guide des tailles complet</a> pour choisir la coupe idéale.</p>
            </div>

            <div x-show="tab === 'livraison'" x-cloak class="mt-6 text-sm text-ink/70">
                <p>Livraison à domicile ou en point relais partout au Sénégal. Voir les modes et délais lors du paiement.</p>
            </div>

            <div x-show="tab === 'avis'" x-cloak class="mt-6 space-y-6">
                @forelse($reviews as $review)
                    <div class="border-b border-ink/10 pb-4">
                        <x-star-rating :rating="$review->rating" />
                        <p class="mt-2 font-semibold text-ink">{{ $review->title }}</p>
                        <p class="mt-1 text-sm text-ink/70">{{ $review->comment }}</p>
                        <p class="mt-2 text-xs text-ink/40">{{ $review->user->name }} — {{ $review->created_at->format('d/m/Y') }}</p>
                    </div>
                @empty
                    <p class="text-sm text-ink/50">Aucun avis pour le moment.</p>
                @endforelse
                {{ $reviews->links() }}

                @if($canReview)
                    <form action="{{ route('reviews.store', $product) }}" method="POST" class="mt-6 space-y-3 rounded-sm bg-white p-4">
                        @csrf
                        <p class="text-sm font-semibold">Laisser un avis</p>
                        <select name="rating" required class="rounded-sm border-ink/15 text-sm">
                            @for ($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}">{{ $i }} étoile{{ $i > 1 ? 's' : '' }}</option>
                            @endfor
                        </select>
                        <input type="text" name="title" placeholder="Titre (optionnel)" class="w-full rounded-sm border-ink/15 text-sm">
                        <textarea name="comment" rows="3" placeholder="Votre commentaire" class="w-full rounded-sm border-ink/15 text-sm"></textarea>
                        <button type="submit" class="btn-outline text-xs">Envoyer mon avis</button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Produits similaires --}}
        @if($relatedProducts->isNotEmpty())
            <div class="mt-14">
                <h2 class="section-title mb-6">Produits similaires</h2>
                <div class="grid grid-cols-2 gap-6 sm:grid-cols-4">
                    @foreach($relatedProducts as $related)
                        <x-product-card :product="$related" />
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    @push('head')
    <script>
        function productForm({ sizes, colors, variants }) {
            return {
                size: sizes[0] ?? null,
                color: colors[0] ?? null,
                qty: 1,
                variants,
                selectedVariantId() {
                    const match = this.variants.find(v => (v.size ?? null) === this.size && (v.color ?? null) === this.color);
                    return match ? match.id : (this.variants[0]?.id ?? null);
                },
                currentStock() {
                    const match = this.variants.find(v => (v.size ?? null) === this.size && (v.color ?? null) === this.color);
                    return match ? match.stock : 0;
                },
            };
        }
    </script>
    @endpush
</x-layouts.shop>
