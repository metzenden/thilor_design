<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAddressRequest;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        $addresses = $request->user()->addresses()->latest()->get();

        return view('account.addresses.index', compact('addresses'));
    }

    public function create()
    {
        return view('account.addresses.create');
    }

    public function store(StoreAddressRequest $request)
    {
        $data = $request->validated();

        if ($request->boolean('is_default')) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        $request->user()->addresses()->create($data);

        return redirect()->route('account.adresses.index')->with('status', 'Adresse ajoutée.');
    }

    public function edit(Address $address)
    {
        $this->authorize('update', $address);

        return view('account.addresses.edit', compact('address'));
    }

    public function update(StoreAddressRequest $request, Address $address)
    {
        $this->authorize('update', $address);

        if ($request->boolean('is_default')) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        $address->update($request->validated());

        return redirect()->route('account.adresses.index')->with('status', 'Adresse mise à jour.');
    }

    public function destroy(Request $request, Address $address)
    {
        $this->authorize('delete', $address);

        $address->delete();

        return back()->with('status', 'Adresse supprimée.');
    }
}
