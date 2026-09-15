@php($shopName = \App\Models\Setting::get('shop_name', 'THILOR DESIGN'))
<header class="border-b border-ink/10 bg-cream" x-data="{ mobileOpen: false }">
    <div class="container-shop flex items-center gap-6 py-4">
        <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0">
            <x-brand-logo :size="44" />
            <span class="hidden flex-col leading-tight sm:flex">
                <span class="font-serif text-lg font-semibold tracking-wide text-ink">{{ $shopName }}</span>
            </span>
        </a>

        <form action="{{ route('catalog.index') }}" method="GET" class="hidden flex-1 md:block">
            <div class="relative">
                <input
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Rechercher un produit..."
                    class="w-full rounded-sm border-ink/15 bg-white py-2.5 pl-4 pr-10 text-sm focus:border-gold focus:ring-gold"
                >
                <button type="submit" class="absolute inset-y-0 right-0 flex items-center pr-3 text-ink/50 hover:text-gold">
                    <x-heroicon-o-magnifying-glass class="h-5 w-5" />
                </button>
            </div>
        </form>

        <div class="ml-auto flex items-center gap-4">
            @auth
                <a href="{{ route('wishlist.index') }}" class="text-ink/70 hover:text-gold" title="Mes favoris">
                    <x-heroicon-o-heart class="h-6 w-6" />
                </a>
                <a href="{{ route('account.index') }}" class="text-ink/70 hover:text-gold" title="Mon compte">
                    <x-heroicon-o-user class="h-6 w-6" />
                </a>
            @else
                <a href="{{ route('login') }}" class="text-ink/70 hover:text-gold" title="Connexion">
                    <x-heroicon-o-user class="h-6 w-6" />
                </a>
            @endauth
            <a href="{{ route('cart.index') }}" class="relative text-ink/70 hover:text-gold" title="Panier">
                <x-heroicon-o-shopping-bag class="h-6 w-6" />
                @if(($headerCartCount ?? 0) > 0)
                    <span class="absolute -right-2 -top-2 flex h-4 w-4 items-center justify-center rounded-full bg-gold text-[10px] font-semibold text-white">
                        {{ $headerCartCount }}
                    </span>
                @endif
            </a>
            <button class="text-ink/70 md:hidden" @click="mobileOpen = !mobileOpen">
                <x-heroicon-o-bars-3 class="h-6 w-6" />
            </button>
        </div>
    </div>

    <nav class="hidden border-t border-ink/10 bg-white md:block">
        <div class="container-shop flex items-center justify-center gap-8 py-3 text-xs font-semibold uppercase tracking-widest text-ink/80">
            <a href="{{ route('home') }}" class="hover:text-gold {{ request()->routeIs('home') ? 'text-gold' : '' }}">Accueil</a>
            @foreach($navCategories ?? [] as $category)
                <a href="{{ route('catalog.category', $category) }}" class="hover:text-gold">{{ $category->name }}</a>
            @endforeach
            <a href="{{ route('collections.index') }}" class="hover:text-gold {{ request()->routeIs('collections.*') ? 'text-gold' : '' }}">Collections</a>
            <a href="{{ route('catalog.promotions') }}" class="hover:text-gold {{ request()->routeIs('catalog.promotions') ? 'text-gold' : '' }}">Promotions</a>
            <a href="{{ route('blog.index') }}" class="hover:text-gold {{ request()->routeIs('blog.*') ? 'text-gold' : '' }}">Blog</a>
            <a href="{{ route('contact.create') }}" class="hover:text-gold {{ request()->routeIs('contact.*') ? 'text-gold' : '' }}">Contact</a>
        </div>
    </nav>

    <div x-show="mobileOpen" x-cloak class="border-t border-ink/10 bg-white md:hidden">
        <form action="{{ route('catalog.index') }}" method="GET" class="px-4 pt-4">
            <input type="search" name="q" placeholder="Rechercher..." class="w-full rounded-sm border-ink/15 text-sm">
        </form>
        <div class="flex flex-col gap-1 px-4 py-4 text-sm font-medium uppercase tracking-wide text-ink/80">
            <a href="{{ route('home') }}" class="py-2">Accueil</a>
            @foreach($navCategories ?? [] as $category)
                <a href="{{ route('catalog.category', $category) }}" class="py-2">{{ $category->name }}</a>
            @endforeach
            <a href="{{ route('collections.index') }}" class="py-2">Collections</a>
            <a href="{{ route('catalog.promotions') }}" class="py-2">Promotions</a>
            <a href="{{ route('blog.index') }}" class="py-2">Blog</a>
            <a href="{{ route('contact.create') }}" class="py-2">Contact</a>
        </div>
    </div>
</header>
