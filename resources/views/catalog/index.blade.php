<x-layouts.shop :title="$title.' — THILOR DESIGN'">
    <div class="container-shop py-8">
        <nav class="mb-4 text-xs text-ink/50">
            <a href="{{ route('home') }}" class="hover:text-gold">Accueil</a>
            <span class="mx-1">/</span>
            <span class="text-ink">{{ $title }}</span>
        </nav>

        <div class="grid gap-8 md:grid-cols-[240px_1fr]">
            {{-- Filtres --}}
            <aside class="space-y-6">
                <form method="GET" id="filters-form">
                    @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif

                    <div>
                        <h3 class="mb-3 text-xs font-semibold uppercase tracking-widest text-ink/60">Catégories</h3>
                        <ul class="space-y-2 text-sm">
                            @foreach($categories as $category)
                                <li>
                                    <label class="flex items-center justify-between gap-2">
                                        <span class="flex items-center gap-2">
                                            <input type="checkbox" name="category[]" value="{{ $category->id }}"
                                                {{ in_array($category->id, (array) request('category', [])) ? 'checked' : '' }}
                                                onchange="document.getElementById('filters-form').submit()">
                                            {{ $category->name }}
                                        </span>
                                        <span class="text-ink/40">({{ $category->products_count }})</span>
                                    </label>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="mt-6">
                        <h3 class="mb-3 text-xs font-semibold uppercase tracking-widest text-ink/60">Taille</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($availableSizes as $size)
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="size[]" value="{{ $size }}" class="peer sr-only"
                                        {{ in_array($size, (array) request('size', [])) ? 'checked' : '' }}
                                        onchange="document.getElementById('filters-form').submit()">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-sm border border-ink/20 text-xs peer-checked:border-gold peer-checked:bg-gold peer-checked:text-white">{{ $size }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-6">
                        <h3 class="mb-3 text-xs font-semibold uppercase tracking-widest text-ink/60">Couleur</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($availableColors as $color)
                                <label class="flex items-center gap-1 text-xs">
                                    <input type="checkbox" name="color[]" value="{{ $color }}"
                                        {{ in_array($color, (array) request('color', [])) ? 'checked' : '' }}
                                        onchange="document.getElementById('filters-form').submit()">
                                    {{ $color }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-6">
                        <h3 class="mb-3 text-xs font-semibold uppercase tracking-widest text-ink/60">Prix (FCFA)</h3>
                        <div class="flex items-center gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="w-full rounded-sm border-ink/15 text-xs">
                            <span>—</span>
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="w-full rounded-sm border-ink/15 text-xs">
                        </div>
                    </div>

                    <div class="mt-6">
                        <h3 class="mb-3 text-xs font-semibold uppercase tracking-widest text-ink/60">Disponibilité</h3>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }}>
                            En stock uniquement
                        </label>
                    </div>

                    <button type="submit" class="btn-outline mt-6 w-full text-xs">Appliquer les filtres</button>
                </form>
            </aside>

            {{-- Résultats --}}
            <div>
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <h1 class="font-serif text-2xl font-semibold">{{ $title }}</h1>
                    <p class="text-sm text-ink/50">Il y a {{ $products->total() }} produits</p>
                    <form method="GET" class="flex items-center gap-2 text-sm">
                        @foreach(request()->except('sort') as $key => $value)
                            @if(is_array($value))
                                @foreach($value as $v)
                                    <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <label for="sort" class="text-ink/50">Trier par :</label>
                        <select name="sort" id="sort" onchange="this.form.submit()" class="rounded-sm border-ink/15 text-sm">
                            <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Plus récents</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Prix croissant</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Prix décroissant</option>
                            <option value="popularity" {{ request('sort') === 'popularity' ? 'selected' : '' }}>Popularité</option>
                        </select>
                    </form>
                </div>

                @if($products->isEmpty())
                    <p class="rounded-sm bg-white p-8 text-center text-ink/50">Aucun produit ne correspond à votre recherche.</p>
                @else
                    <div class="grid grid-cols-2 gap-6 sm:grid-cols-3">
                        @foreach($products as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $products->onEachSide(1)->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.shop>
