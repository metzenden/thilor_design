<x-layouts.shop title="Contact — THILOR DESIGN">
    <div class="container-shop py-12">
        <div class="mx-auto max-w-2xl">
            <h1 class="section-title text-center">Contactez-nous</h1>
            <p class="mt-2 text-center text-sm text-ink/60">Une question ? Notre équipe vous répond rapidement.</p>

            <div class="mt-6 grid gap-6 sm:grid-cols-3 text-center text-sm">
                <div>
                    <x-heroicon-o-phone class="mx-auto h-5 w-5 text-gold" />
                    <p class="mt-1">{{ \App\Models\Setting::get('shop_phone') }}</p>
                </div>
                <div>
                    <x-heroicon-o-envelope class="mx-auto h-5 w-5 text-gold" />
                    <p class="mt-1">{{ \App\Models\Setting::get('shop_email') }}</p>
                </div>
                <div>
                    <x-heroicon-o-map-pin class="mx-auto h-5 w-5 text-gold" />
                    <p class="mt-1">{{ \App\Models\Setting::get('shop_address') }}</p>
                </div>
            </div>

            <form action="{{ route('contact.store') }}" method="POST" class="mt-8 space-y-4 rounded-sm bg-white p-6">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="name" value="Nom" />
                        <x-text-input id="name" name="name" class="mt-1 w-full" value="{{ old('name') }}" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email" type="email" name="email" class="mt-1 w-full" value="{{ old('email') }}" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="phone" value="Téléphone (optionnel)" />
                        <x-text-input id="phone" name="phone" class="mt-1 w-full" value="{{ old('phone') }}" />
                    </div>
                    <div>
                        <x-input-label for="subject" value="Sujet" />
                        <x-text-input id="subject" name="subject" class="mt-1 w-full" value="{{ old('subject') }}" />
                    </div>
                </div>
                <div>
                    <x-input-label for="message" value="Message" />
                    <textarea id="message" name="message" rows="5" required class="mt-1 w-full rounded-sm border-ink/15">{{ old('message') }}</textarea>
                    <x-input-error :messages="$errors->get('message')" class="mt-1" />
                </div>
                <button type="submit" class="btn-gold">Envoyer le message</button>
            </form>
        </div>
    </div>
</x-layouts.shop>
