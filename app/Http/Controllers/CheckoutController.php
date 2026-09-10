<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutInformationRequest;
use App\Http\Requests\CheckoutPaymentRequest;
use App\Models\Order;
use App\Models\ShippingMethod;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\Payments\PaymentGatewayManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    private const SESSION_KEY = 'checkout.information';

    public function __construct(private readonly CartService $cartService) {}

    public function index()
    {
        $cart = $this->cartService->current();

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        $shippingMethods = ShippingMethod::where('is_active', true)->orderBy('position')->get();
        $defaultAddress = Auth::check() ? Auth::user()->defaultAddress() : null;
        $old = session(self::SESSION_KEY, []);

        return view('checkout.information', compact('cart', 'shippingMethods', 'defaultAddress', 'old'));
    }

    public function storeInformation(CheckoutInformationRequest $request): RedirectResponse
    {
        session([self::SESSION_KEY => $request->validated()]);

        return redirect()->route('checkout.payment');
    }

    public function payment(PaymentGatewayManager $gateways)
    {
        $cart = $this->cartService->current();
        $information = session(self::SESSION_KEY);

        if (! $information || $cart->items->isEmpty()) {
            return redirect()->route('checkout.index');
        }

        $shippingMethod = ShippingMethod::findOrFail($information['shipping_method_id']);

        return view('checkout.payment', [
            'cart' => $cart,
            'information' => $information,
            'shippingMethod' => $shippingMethod,
            'gateways' => $gateways->all(),
        ]);
    }

    public function placeOrder(CheckoutPaymentRequest $request, OrderService $orderService, PaymentGatewayManager $gateways): RedirectResponse
    {
        $cart = $this->cartService->current();
        $information = session(self::SESSION_KEY);

        if (! $information || $cart->items->isEmpty()) {
            return redirect()->route('checkout.index');
        }

        $gateway = $gateways->get($request->validated('payment_method'));

        if (! $gateway->isAvailable()) {
            return back()->with('error', "Le moyen de paiement « {$gateway->label()} » n'est pas encore disponible. Merci de choisir le paiement à la livraison.");
        }

        $shippingMethod = ShippingMethod::findOrFail($information['shipping_method_id']);

        try {
            $order = $orderService->createFromCart(
                cart: $cart,
                shippingMethod: $shippingMethod,
                customer: [
                    'name' => $information['name'],
                    'email' => $information['email'],
                    'phone' => $information['phone'],
                ],
                shippingAddress: [
                    'address_line' => $information['address_line'],
                    'city' => $information['city'],
                    'district' => $information['district'] ?? null,
                    'postal_code' => $information['postal_code'] ?? null,
                    'country' => $information['country'] ?? 'Sénégal',
                ],
                gateway: $gateway,
                userId: Auth::id(),
            );
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        session()->forget(self::SESSION_KEY);

        return redirect()->route('checkout.confirmation', $order->order_number);
    }

    public function confirmation(Order $order)
    {
        if (Auth::check()) {
            abort_unless($order->user_id === Auth::id(), 403);
        }

        $order->load(['items', 'payment', 'shippingMethod']);

        return view('checkout.confirmation', compact('order'));
    }
}
