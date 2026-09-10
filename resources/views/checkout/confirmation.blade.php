<x-layouts.shop title="Commande confirmée — THILOR DESIGN">
    <div class="container-shop py-16">
        <x-checkout.steps current="confirmation" />

        <div class="mx-auto max-w-lg text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-100 text-green-700">
                <x-heroicon-o-check class="h-8 w-8" />
            </div>
            <h1 class="mt-4 font-serif text-2xl font-semibold">Merci pour votre commande !</h1>
            <p class="mt-2 text-sm text-ink/60">Votre commande a été enregistrée avec succès.</p>
            <p class="mt-1 text-sm text-ink/60">Un email de confirmation a été envoyé à {{ $order->customer_email }}</p>

            <div class="mt-6 rounded-sm bg-white p-6 text-left text-sm">
                <div class="flex justify-between border-b border-ink/10 pb-3">
                    <span class="text-ink/50">N° de commande</span>
                    <span class="font-semibold">#{{ $order->order_number }}</span>
                </div>
                <div class="mt-3 space-y-2">
                    @foreach($order->items as $item)
                        <div class="flex justify-between">
                            <span class="text-ink/70">{{ $item->product_name }} @if($item->variant_label)<span class="text-ink/40">({{ $item->variant_label }})</span>@endif × {{ $item->quantity }}</span>
                            <span>{{ number_format($item->line_total, 0, ',', ' ') }} FCFA</span>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3 flex justify-between border-t border-ink/10 pt-3 font-semibold">
                    <span>Total</span>
                    <span>{{ number_format($order->total, 0, ',', ' ') }} FCFA</span>
                </div>
                <p class="mt-2 text-xs text-ink/50">Paiement : {{ $order->payment?->method === 'cash_on_delivery' ? 'Paiement à la livraison' : $order->payment?->method }}</p>
            </div>

            <a href="{{ route('home') }}" class="btn-gold mt-8 inline-flex">Retour à l'accueil</a>

            <p class="mt-6 text-xs text-ink/50">
                Besoin d'aide ? Contactez-nous au {{ \App\Models\Setting::get('shop_phone') }}
            </p>
        </div>
    </div>
</x-layouts.shop>
