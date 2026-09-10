<x-layouts.shop title="Ajouter une adresse — THILOR DESIGN">
    <div class="container-shop py-8">
        <h1 class="mb-6 font-serif text-2xl font-semibold">Mon compte</h1>
        <div class="flex flex-col gap-6 md:flex-row">
            <x-account.sidebar active="addresses" />

            <div class="flex-1 rounded-sm bg-white p-6">
                <h2 class="mb-4 font-serif text-lg font-semibold">Ajouter une adresse</h2>
                <form action="{{ route('account.adresses.store') }}" method="POST" class="space-y-6">
                    @csrf
                    @include('account.addresses._form')
                    <button type="submit" class="btn-gold">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.shop>
