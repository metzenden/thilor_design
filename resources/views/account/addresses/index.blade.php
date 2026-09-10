<x-layouts.shop title="Mes adresses — THILOR DESIGN">
    <div class="container-shop py-8">
        <h1 class="mb-6 font-serif text-2xl font-semibold">Mon compte</h1>
        <div class="flex flex-col gap-6 md:flex-row">
            <x-account.sidebar active="addresses" />

            <div class="flex-1 rounded-sm bg-white p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-serif text-lg font-semibold">Mes adresses</h2>
                    <a href="{{ route('account.adresses.create') }}" class="btn-outline text-xs">Ajouter une adresse</a>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    @forelse($addresses as $address)
                        <div class="rounded-sm border border-ink/10 p-4 text-sm">
                            <div class="flex items-center justify-between">
                                <p class="font-semibold">{{ $address->label }}</p>
                                @if($address->is_default)
                                    <span class="rounded-full bg-gold-50 px-2 py-0.5 text-xs text-gold-700">Par défaut</span>
                                @endif
                            </div>
                            <p class="mt-1 text-ink/70">{{ $address->full_name }}</p>
                            <p class="text-ink/70">{{ $address->phone }}</p>
                            <p class="text-ink/70">{{ $address->address_line }}, {{ $address->district }} {{ $address->city }}</p>
                            <div class="mt-3 flex gap-3 text-xs">
                                <a href="{{ route('account.adresses.edit', $address) }}" class="text-gold hover:underline">Modifier</a>
                                <form action="{{ route('account.adresses.destroy', $address) }}" method="POST" onsubmit="return confirm('Supprimer cette adresse ?');">
                                    @csrf @method('DELETE')
                                    <button class="text-bordeaux hover:underline">Supprimer</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-ink/50">Aucune adresse enregistrée.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.shop>
