<x-layouts.shop title="Mon compte — THILOR DESIGN">
    <div class="container-shop py-8">
        <h1 class="mb-6 font-serif text-2xl font-semibold">Mon compte</h1>
        <div class="flex flex-col gap-6 md:flex-row">
            <x-account.sidebar active="dashboard" />

            <div class="flex-1 space-y-6">
                <div class="rounded-sm bg-white p-6">
                    <p class="text-sm text-ink/60">Bonjour {{ $user->name }},</p>
                    <p class="mt-1 text-sm text-ink/60">Bienvenue dans votre espace client THILOR DESIGN.</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <a href="{{ route('account.orders') }}" class="rounded-sm bg-white p-5 hover:shadow">
                        <p class="text-3xl font-serif font-semibold text-gold">{{ $user->orders()->count() }}</p>
                        <p class="mt-1 text-sm text-ink/60">Commandes</p>
                    </a>
                    <a href="{{ route('account.adresses.index') }}" class="rounded-sm bg-white p-5 hover:shadow">
                        <p class="text-3xl font-serif font-semibold text-gold">{{ $addressesCount }}</p>
                        <p class="mt-1 text-sm text-ink/60">Adresses</p>
                    </a>
                    <a href="{{ route('wishlist.index') }}" class="rounded-sm bg-white p-5 hover:shadow">
                        <p class="text-3xl font-serif font-semibold text-gold">{{ $wishlistCount }}</p>
                        <p class="mt-1 text-sm text-ink/60">Favoris</p>
                    </a>
                </div>

                <div class="rounded-sm bg-white p-6">
                    <h2 class="mb-4 font-serif text-lg font-semibold">Commandes récentes</h2>
                    @forelse($recentOrders as $order)
                        <a href="{{ route('account.orders.show', $order) }}" class="flex items-center justify-between border-b border-ink/10 py-3 text-sm last:border-0">
                            <span>#{{ $order->order_number }}</span>
                            <span class="text-ink/50">{{ $order->placed_at?->format('d/m/Y') }}</span>
                            <span class="font-medium">{{ number_format($order->total, 0, ',', ' ') }} FCFA</span>
                        </a>
                    @empty
                        <p class="text-sm text-ink/50">Vous n'avez pas encore passé de commande.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.shop>
