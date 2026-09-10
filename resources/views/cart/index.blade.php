<x-layouts.shop title="Panier — THILOR DESIGN">
    <div class="container-shop py-8">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="font-serif text-2xl font-semibold">Votre panier ({{ $cart->items_count }})</h1>
            <a href="{{ route('catalog.index') }}" class="text-sm text-gold hover:underline">Continuer mes achats</a>
        </div>

        @if($cart->items->isEmpty())
            <div class="rounded-sm bg-white p-10 text-center">
                <p class="text-ink/60">Votre panier est vide.</p>
                <a href="{{ route('catalog.index') }}" class="btn-gold mt-4 inline-flex">Découvrir la collection</a>
            </div>
        @else
            <div class="grid gap-8 lg:grid-cols-[1fr_360px]">
                <div class="space-y-4">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-ink/10 text-left text-xs uppercase tracking-wide text-ink/50">
                                <th class="pb-3">Produit</th>
                                <th class="pb-3">Prix</th>
                                <th class="pb-3">Quantité</th>
                                <th class="pb-3">Total</th>
                                <th class="pb-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cart->items as $item)
                                <tr class="border-b border-ink/10">
                                    <td class="py-4">
                                        <div class="flex items-center gap-3">
                                            @php($image = $item->variant->product->images->first())
                                            @if($image)
                                                <img src="{{ $image->url }}" alt="" class="h-16 w-14 rounded-sm object-cover">
                                            @endif
                                            <div>
                                                <a href="{{ route('products.show', $item->variant->product) }}" class="font-medium text-ink hover:text-gold">
                                                    {{ $item->variant->product->name }}
                                                </a>
                                                <p class="text-xs text-ink/50">{{ $item->variant->label }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ number_format($item->variant->price, 0, ',', ' ') }} FCFA</td>
                                    <td>
                                        <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex w-fit items-center rounded-sm border border-ink/20">
                                            @csrf @method('PATCH')
                                            <button type="button" class="px-2 py-1" onclick="this.form.quantity.value=Math.max(0, this.form.quantity.valueAsNumber - 1); this.form.requestSubmit()">-</button>
                                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="0" max="{{ $item->variant->stock }}" class="w-10 border-0 p-1 text-center text-xs focus:ring-0" onchange="this.form.requestSubmit()">
                                            <button type="button" class="px-2 py-1" onclick="this.form.quantity.value=this.form.quantity.valueAsNumber + 1; this.form.requestSubmit()">+</button>
                                        </form>
                                    </td>
                                    <td class="font-medium">{{ number_format($item->line_total, 0, ',', ' ') }} FCFA</td>
                                    <td>
                                        <form action="{{ route('cart.destroy', $item->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button class="text-ink/40 hover:text-bordeaux"><x-heroicon-o-trash class="h-4 w-4" /></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="h-fit rounded-sm bg-white p-6">
                    <h2 class="mb-4 font-serif text-lg font-semibold">Résumé de la commande</h2>
                    <dl class="space-y-2 text-sm text-ink/70">
                        <div class="flex justify-between">
                            <dt>Sous-total</dt>
                            <dd>{{ number_format($cart->subtotal, 0, ',', ' ') }} FCFA</dd>
                        </div>
                        @if($cart->coupon)
                            <div class="flex justify-between text-green-700">
                                <dt>Remise ({{ $cart->coupon->code }})</dt>
                                <dd>-{{ number_format($cart->discount, 0, ',', ' ') }} FCFA</dd>
                            </div>
                        @endif
                    </dl>

                    <div class="mt-4">
                        @if($cart->coupon)
                            <form action="{{ route('cart.coupon.remove') }}" method="POST" class="flex items-center justify-between text-xs">
                                @csrf @method('DELETE')
                                <span>Code : <strong>{{ $cart->coupon->code }}</strong></span>
                                <button class="text-bordeaux underline">Retirer</button>
                            </form>
                        @else
                            <form action="{{ route('cart.coupon.apply') }}" method="POST" class="flex gap-2">
                                @csrf
                                <input type="text" name="code" placeholder="Saisir un code promo" class="w-full rounded-sm border-ink/15 text-xs">
                                <button type="submit" class="btn-outline shrink-0 text-xs">Appliquer</button>
                            </form>
                        @endif
                    </div>

                    <div class="mt-4 flex justify-between border-t border-ink/10 pt-4 font-serif text-lg font-semibold">
                        <span>Total</span>
                        <span>{{ number_format(max($cart->subtotal - $cart->discount, 0), 0, ',', ' ') }} FCFA</span>
                    </div>
                    <p class="mt-1 text-xs text-ink/40">Livraison calculée à l'étape suivante</p>

                    <a href="{{ route('checkout.index') }}" class="btn-gold mt-5 w-full">Passer la commande</a>

                    <div class="mt-4 flex items-center gap-2 text-ink/40">
                        <x-heroicon-o-lock-closed class="h-4 w-4" />
                        <span class="text-xs">Paiement sécurisé</span>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layouts.shop>
