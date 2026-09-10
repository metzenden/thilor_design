<x-layouts.shop :title="'Commande #'.$order->order_number.' — THILOR DESIGN'">
    <div class="container-shop py-8">
        <h1 class="mb-6 font-serif text-2xl font-semibold">Mon compte</h1>
        <div class="flex flex-col gap-6 md:flex-row">
            <x-account.sidebar active="orders" />

            <div class="flex-1 space-y-6">
                <div class="rounded-sm bg-white p-6">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h2 class="font-serif text-lg font-semibold">Commande #{{ $order->order_number }}</h2>
                        <x-order-status-badge :status="$order->status" />
                    </div>
                    <p class="mt-1 text-sm text-ink/50">Passée le {{ $order->placed_at?->format('d/m/Y à H:i') }}</p>

                    <div class="mt-4 divide-y divide-ink/10">
                        @foreach($order->items as $item)
                            <div class="flex justify-between py-3 text-sm">
                                <span>{{ $item->product_name }} @if($item->variant_label)<span class="text-ink/40">({{ $item->variant_label }})</span>@endif × {{ $item->quantity }}</span>
                                <span>{{ number_format($item->line_total, 0, ',', ' ') }} FCFA</span>
                            </div>
                        @endforeach
                    </div>

                    <dl class="mt-4 space-y-1 border-t border-ink/10 pt-4 text-sm text-ink/70">
                        <div class="flex justify-between"><dt>Sous-total</dt><dd>{{ number_format($order->subtotal, 0, ',', ' ') }} FCFA</dd></div>
                        <div class="flex justify-between"><dt>Livraison</dt><dd>{{ number_format($order->shipping_cost, 0, ',', ' ') }} FCFA</dd></div>
                        @if($order->discount_amount)
                            <div class="flex justify-between"><dt>Remise</dt><dd>-{{ number_format($order->discount_amount, 0, ',', ' ') }} FCFA</dd></div>
                        @endif
                        <div class="flex justify-between font-semibold text-ink"><dt>Total</dt><dd>{{ number_format($order->total, 0, ',', ' ') }} FCFA</dd></div>
                    </dl>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-sm bg-white p-6">
                        <h3 class="mb-2 text-sm font-semibold uppercase tracking-wide text-ink/50">Livraison</h3>
                        <p class="text-sm text-ink/70">{{ $order->shippingMethod?->name }}</p>
                        <p class="text-sm text-ink/70">{{ $order->shipping_address_line }}, {{ $order->shipping_district }} {{ $order->shipping_city }}</p>
                    </div>
                    <div class="rounded-sm bg-white p-6">
                        <h3 class="mb-2 text-sm font-semibold uppercase tracking-wide text-ink/50">Paiement</h3>
                        <p class="text-sm text-ink/70">{{ $order->payment?->method === 'cash_on_delivery' ? 'Paiement à la livraison' : $order->payment?->method }}</p>
                        <x-order-status-badge :status="$order->payment?->status ?? 'pending'" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.shop>
