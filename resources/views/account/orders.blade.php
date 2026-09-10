<x-layouts.shop title="Mes commandes — THILOR DESIGN">
    <div class="container-shop py-8">
        <h1 class="mb-6 font-serif text-2xl font-semibold">Mon compte</h1>
        <div class="flex flex-col gap-6 md:flex-row">
            <x-account.sidebar active="orders" />

            <div class="flex-1 rounded-sm bg-white p-6">
                <h2 class="mb-4 font-serif text-lg font-semibold">Mes commandes</h2>

                @forelse($orders as $order)
                    <a href="{{ route('account.orders.show', $order) }}" class="flex items-center justify-between gap-4 border-b border-ink/10 py-4 text-sm last:border-0">
                        <span class="font-medium">#{{ $order->order_number }}</span>
                        <x-order-status-badge :status="$order->status" />
                        <span class="text-ink/50">{{ $order->placed_at?->format('d/m/Y') }}</span>
                        <span class="font-semibold">{{ number_format($order->total, 0, ',', ' ') }} FCFA</span>
                        <span class="text-gold">Voir les détails &rsaquo;</span>
                    </a>
                @empty
                    <p class="text-sm text-ink/50">Vous n'avez pas encore passé de commande.</p>
                @endforelse

                <div class="mt-6">{{ $orders->links() }}</div>
            </div>
        </div>
    </div>
</x-layouts.shop>
