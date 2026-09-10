@props(['active'])

@php
    $links = [
        'dashboard' => ['label' => 'Tableau de bord', 'route' => route('account.index')],
        'orders' => ['label' => 'Mes commandes', 'route' => route('account.orders')],
        'addresses' => ['label' => 'Mes adresses', 'route' => route('account.adresses.index')],
        'profile' => ['label' => 'Mes informations', 'route' => route('profile.edit')],
        'wishlist' => ['label' => 'Mes favoris', 'route' => route('wishlist.index')],
    ];
@endphp

<aside class="w-full shrink-0 rounded-sm bg-white p-4 md:w-56">
    <nav class="space-y-1 text-sm">
        @foreach($links as $key => $link)
            <a href="{{ $link['route'] }}" class="block rounded-sm px-3 py-2 {{ $active === $key ? 'bg-gold-50 font-semibold text-gold' : 'text-ink/70 hover:bg-cream' }}">
                {{ $link['label'] }}
            </a>
        @endforeach
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="block w-full rounded-sm px-3 py-2 text-left text-ink/70 hover:bg-cream">Déconnexion</button>
        </form>
    </nav>
</aside>
