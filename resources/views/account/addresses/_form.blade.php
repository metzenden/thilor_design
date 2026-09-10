@props(['address' => null])

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="label" value="Libellé" />
        <x-text-input id="label" name="label" class="mt-1 w-full" value="{{ old('label', $address->label ?? 'Domicile') }}" />
    </div>
    <div>
        <x-input-label for="full_name" value="Nom complet" />
        <x-text-input id="full_name" name="full_name" class="mt-1 w-full" value="{{ old('full_name', $address->full_name ?? '') }}" required />
        <x-input-error :messages="$errors->get('full_name')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="phone" value="Téléphone" />
        <x-text-input id="phone" name="phone" class="mt-1 w-full" value="{{ old('phone', $address->phone ?? '') }}" required />
        <x-input-error :messages="$errors->get('phone')" class="mt-1" />
    </div>
    <div class="sm:col-span-2">
        <x-input-label for="address_line" value="Adresse" />
        <x-text-input id="address_line" name="address_line" class="mt-1 w-full" value="{{ old('address_line', $address->address_line ?? '') }}" required />
        <x-input-error :messages="$errors->get('address_line')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="city" value="Ville" />
        <x-text-input id="city" name="city" class="mt-1 w-full" value="{{ old('city', $address->city ?? '') }}" required />
        <x-input-error :messages="$errors->get('city')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="district" value="Quartier" />
        <x-text-input id="district" name="district" class="mt-1 w-full" value="{{ old('district', $address->district ?? '') }}" />
    </div>
    <div>
        <x-input-label for="postal_code" value="Code postal" />
        <x-text-input id="postal_code" name="postal_code" class="mt-1 w-full" value="{{ old('postal_code', $address->postal_code ?? '') }}" />
    </div>
    <div>
        <x-input-label for="country" value="Pays" />
        <x-text-input id="country" name="country" class="mt-1 w-full" value="{{ old('country', $address->country ?? 'Sénégal') }}" />
    </div>
    <div class="sm:col-span-2">
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_default" value="1" {{ old('is_default', $address->is_default ?? false) ? 'checked' : '' }}>
            Définir comme adresse par défaut
        </label>
    </div>
</div>
