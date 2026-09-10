@props(['cart', 'shippingMethod' => null])

<div class="h-fit rounded-sm bg-white p-6">
    <h2 class="mb-4 font-serif text-lg font-semibold">Résumé de la commande</h2>

    <ul class="max-h-64 space-y-3 overflow-y-auto text-sm">
        @foreach($cart->items as $item)
            <li class="flex items-center justify-between gap-2">
                <span class="flex-1 text-ink/70">{{ $item->variant->product->name }} <span class="text-ink/40">× {{ $item->quantity }}</span></span>
                <span class="font-medium">{{ number_format($item->line_total, 0, ',', ' ') }} FCFA</span>
            </li>
        @endforeach
    </ul>

    <dl class="mt-4 space-y-2 border-t border-ink/10 pt-4 text-sm text-ink/70">
        <div class="flex justify-between">
            <dt>Sous-total</dt>
            <dd>{{ number_format($cart->subtotal, 0, ',', ' ') }} FCFA</dd>
        </div>
        @if($shippingMethod)
            <div class="flex justify-between">
                <dt>Livraison ({{ $shippingMethod->name }})</dt>
                <dd>{{ number_format($shippingMethod->cost, 0, ',', ' ') }} FCFA</dd>
            </div>
        @endif
        @if($cart->coupon)
            <div class="flex justify-between text-green-700">
                <dt>Remise</dt>
                <dd>-{{ number_format($cart->discount, 0, ',', ' ') }} FCFA</dd>
            </div>
        @endif
    </dl>

    <div class="mt-4 flex justify-between border-t border-ink/10 pt-4 font-serif text-lg font-semibold">
        <span>Total</span>
        <span>{{ number_format(max($cart->subtotal - $cart->discount, 0) + ($shippingMethod->cost ?? 0), 0, ',', ' ') }} FCFA</span>
    </div>
</div>
