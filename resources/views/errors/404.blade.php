<x-layouts.shop title="Page introuvable — THILOR DESIGN">
    <div class="container-shop flex flex-col items-center justify-center py-24 text-center">
        <p class="font-serif text-6xl font-semibold text-gold">404</p>
        <h1 class="mt-4 text-xl font-semibold text-ink">Cette page est introuvable</h1>
        <p class="mt-2 max-w-md text-sm text-ink/60">
            La page que vous recherchez n'existe pas ou a été déplacée. Retournez à l'accueil pour continuer votre shopping.
        </p>
        <a href="{{ route('home') }}" class="btn-gold mt-6">Retour à l'accueil</a>
    </div>
</x-layouts.shop>
