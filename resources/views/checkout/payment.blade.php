<x-layouts.shop title="Commande — Paiement — THILOR DESIGN">
    <div class="container-shop py-8">
        <x-checkout.steps current="paiement" />

        <div class="grid gap-8 lg:grid-cols-[1fr_360px]">
            <div class="space-y-8">
                <section class="rounded-sm bg-white p-6">
                    <h2 class="mb-4 font-serif text-lg font-semibold">Adresse de livraison</h2>
                    <p class="text-sm text-ink/70">{{ $information['name'] }} — {{ $information['phone'] }}</p>
                    <p class="text-sm text-ink/70">{{ $information['address_line'] }}, {{ $information['district'] }} {{ $information['city'] }}, {{ $information['country'] ?? 'Sénégal' }}</p>
                    <a href="{{ route('checkout.index') }}" class="mt-2 inline-block text-xs text-gold hover:underline">Modifier</a>
                </section>

                <form action="{{ route('checkout.payment.store') }}" method="POST">
                    @csrf
                    <section class="rounded-sm bg-white p-6">
                        <h2 class="mb-4 font-serif text-lg font-semibold">Méthode de paiement</h2>
                        <div class="space-y-3">
                            @foreach($gateways as $gateway)
                                <label class="flex items-center justify-between rounded-sm border border-ink/15 p-4 {{ $gateway->isAvailable() ? 'has-[:checked]:border-gold has-[:checked]:bg-gold-50' : 'cursor-not-allowed opacity-50' }}">
                                    <span class="flex items-center gap-3">
                                        <input type="radio" name="payment_method" value="{{ $gateway->code() }}"
                                            {{ $loop->first ? 'checked' : '' }}
                                            {{ $gateway->isAvailable() ? '' : 'disabled' }}>
                                        <span>
                                            {{ $gateway->label() }}
                                            @unless($gateway->isAvailable())
                                                <span class="ml-2 text-xs text-ink/40">(bientôt disponible)</span>
                                            @endunless
                                        </span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </section>

                    <button type="submit" class="btn-gold mt-6 w-full">Confirmer ma commande</button>
                </form>
            </div>

            <x-checkout.summary :cart="$cart" :shippingMethod="$shippingMethod" />
        </div>
    </div>
</x-layouts.shop>
