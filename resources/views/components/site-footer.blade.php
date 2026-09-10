@php($shopName = \App\Models\Setting::get('shop_name', 'THILOR DESIGN'))
<footer class="border-t border-ink/10 bg-white">
    <div class="container-shop grid grid-cols-2 gap-8 py-12 text-sm sm:grid-cols-3 lg:grid-cols-6">
        <div class="col-span-2 lg:col-span-2">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-gold font-serif text-base font-semibold text-gold">TD</span>
                <span class="font-serif text-base font-semibold text-ink">{{ $shopName }}</span>
            </div>
            <p class="mt-3 text-ink/60">{{ \App\Models\Setting::get('shop_tagline', "L'élégance africaine à votre style") }}</p>
        </div>

        <div>
            <h3 class="mb-3 text-xs font-semibold uppercase tracking-widest text-ink/50">Informations</h3>
            <ul class="space-y-2 text-ink/70">
                <li><a href="{{ route('pages.show', 'a-propos') }}" class="hover:text-gold">À propos</a></li>
                <li><a href="{{ route('pages.show', 'livraison') }}" class="hover:text-gold">Livraison</a></li>
                <li><a href="{{ route('pages.show', 'retours-remboursements') }}" class="hover:text-gold">Retours &amp; remboursements</a></li>
                <li><a href="{{ route('pages.show', 'conditions-generales') }}" class="hover:text-gold">Conditions générales</a></li>
                <li><a href="{{ route('pages.show', 'politique-de-confidentialite') }}" class="hover:text-gold">Politique de confidentialité</a></li>
            </ul>
        </div>

        <div>
            <h3 class="mb-3 text-xs font-semibold uppercase tracking-widest text-ink/50">Service client</h3>
            <ul class="space-y-2 text-ink/70">
                <li><a href="{{ route('contact.create') }}" class="hover:text-gold">Contact</a></li>
                <li><a href="{{ route('pages.show', 'faq') }}" class="hover:text-gold">FAQ</a></li>
                <li><a href="{{ route('account.orders') }}" class="hover:text-gold">Suivi de commande</a></li>
                <li><a href="{{ route('pages.show', 'guide-des-tailles') }}" class="hover:text-gold">Guide des tailles</a></li>
            </ul>
        </div>

        <div>
            <h3 class="mb-3 text-xs font-semibold uppercase tracking-widest text-ink/50">Nos engagements</h3>
            <ul class="space-y-2 text-ink/70">
                <li>Qualité premium</li>
                <li>Paiement sécurisé</li>
                <li>Satisfait ou remboursé</li>
                <li>Livraison rapide</li>
            </ul>
        </div>

        <div>
            <h3 class="mb-3 text-xs font-semibold uppercase tracking-widest text-ink/50">Suivez-nous</h3>
            <div class="flex gap-3 text-ink/60">
                <a href="{{ \App\Models\Setting::get('social_facebook', '#') }}" class="hover:text-gold" aria-label="Facebook"><x-heroicon-o-globe-alt class="h-5 w-5" /></a>
                <a href="{{ \App\Models\Setting::get('social_instagram', '#') }}" class="hover:text-gold" aria-label="Instagram"><x-heroicon-o-camera class="h-5 w-5" /></a>
                <a href="{{ \App\Models\Setting::get('social_tiktok', '#') }}" class="hover:text-gold" aria-label="TikTok"><x-heroicon-o-musical-note class="h-5 w-5" /></a>
                <a href="{{ \App\Models\Setting::get('social_youtube', '#') }}" class="hover:text-gold" aria-label="YouTube"><x-heroicon-o-play-circle class="h-5 w-5" /></a>
            </div>

            <h3 class="mb-2 mt-6 text-xs font-semibold uppercase tracking-widest text-ink/50">Newsletter</h3>
            <form action="{{ route('newsletter.store') }}" method="POST" class="flex gap-1">
                @csrf
                <input type="email" name="email" required placeholder="Votre email" class="w-full min-w-0 rounded-sm border-ink/15 text-xs">
                <button type="submit" class="shrink-0 rounded-sm bg-gold px-3 py-2 text-xs font-semibold text-white hover:bg-gold-600">S'inscrire</button>
            </form>
        </div>
    </div>

    <div class="border-t border-ink/10 py-4">
        <div class="container-shop flex flex-col items-center justify-between gap-2 text-xs text-ink/50 sm:flex-row">
            <p>&copy; {{ now()->year }} {{ $shopName }}. Tous droits réservés.</p>
            <p>Conçu avec ❤ en Afrique</p>
        </div>
    </div>
</footer>
