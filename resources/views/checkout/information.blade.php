<x-layouts.shop title="Commande — Informations — THILOR DESIGN">
    <div class="container-shop py-8">
        <x-checkout.steps current="livraison" />

        <div class="grid gap-8 lg:grid-cols-[1fr_360px]">
            <form action="{{ route('checkout.information.store') }}" method="POST" class="space-y-8">
                @csrf

                <section class="rounded-sm bg-white p-6">
                    <h2 class="mb-4 font-serif text-lg font-semibold">Adresse de livraison</h2>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <x-input-label for="name" value="Nom complet" />
                            <x-text-input id="name" name="name" class="mt-1 w-full" value="{{ old('name', $defaultAddress->full_name ?? auth()->user()->name ?? '') }}" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="email" value="Email" />
                            <x-text-input id="email" type="email" name="email" class="mt-1 w-full" value="{{ old('email', auth()->user()->email ?? '') }}" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="phone" value="Téléphone" />
                            <x-text-input id="phone" name="phone" class="mt-1 w-full" value="{{ old('phone', $defaultAddress->phone ?? auth()->user()->phone ?? '') }}" required />
                            <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                        </div>
                        <div class="sm:col-span-2">
                            <x-input-label for="address_line" value="Adresse" />
                            <x-text-input id="address_line" name="address_line" class="mt-1 w-full" value="{{ old('address_line', $defaultAddress->address_line ?? '') }}" required />
                            <x-input-error :messages="$errors->get('address_line')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="city" value="Ville" />
                            <x-text-input id="city" name="city" class="mt-1 w-full" value="{{ old('city', $defaultAddress->city ?? '') }}" required />
                            <x-input-error :messages="$errors->get('city')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="district" value="Quartier" />
                            <x-text-input id="district" name="district" class="mt-1 w-full" value="{{ old('district', $defaultAddress->district ?? '') }}" />
                        </div>
                        <div>
                            <x-input-label for="postal_code" value="Code postal" />
                            <x-text-input id="postal_code" name="postal_code" class="mt-1 w-full" value="{{ old('postal_code', $defaultAddress->postal_code ?? '') }}" />
                        </div>
                        <div>
                            <x-input-label for="country" value="Pays" />
                            <x-text-input id="country" name="country" class="mt-1 w-full" value="{{ old('country', $defaultAddress->country ?? 'Sénégal') }}" />
                        </div>
                    </div>
                </section>

                <section class="rounded-sm bg-white p-6">
                    <h2 class="mb-4 font-serif text-lg font-semibold">Mode de livraison</h2>
                    <div class="space-y-3">
                        @foreach($shippingMethods as $method)
                            <label class="flex items-center justify-between rounded-sm border border-ink/15 p-4 has-[:checked]:border-gold has-[:checked]:bg-gold-50">
                                <span class="flex items-center gap-3">
                                    <input type="radio" name="shipping_method_id" value="{{ $method->id }}" {{ old('shipping_method_id', $shippingMethods->first()?->id) == $method->id ? 'checked' : '' }} required>
                                    <span>
                                        <span class="block font-medium">{{ $method->name }}</span>
                                        <span class="block text-xs text-ink/50">{{ $method->delay_label }}</span>
                                    </span>
                                </span>
                                <span class="font-semibold">{{ number_format($method->cost, 0, ',', ' ') }} FCFA</span>
                            </label>
                        @endforeach
                    </div>
                </section>

                <button type="submit" class="btn-gold w-full">Continuer</button>
            </form>

            <x-checkout.summary :cart="$cart" />
        </div>
    </div>
</x-layouts.shop>
