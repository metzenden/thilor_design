<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayContract;
use App\Models\Order;
use App\Models\Payment;

/**
 * Seul moyen de paiement réellement fonctionnel au lancement (le cahier des charges
 * l'exige explicitement). Le paiement reste "pending" jusqu'à encaissement effectif
 * à la livraison, confirmé manuellement par l'administrateur dans le back-office.
 */
class CashOnDeliveryGateway implements PaymentGatewayContract
{
    public function code(): string
    {
        return 'cash_on_delivery';
    }

    public function label(): string
    {
        return 'Paiement à la livraison';
    }

    public function initiate(Order $order): Payment
    {
        return $order->payments()->create([
            'method' => $this->code(),
            'status' => 'pending',
            'amount' => $order->total,
        ]);
    }

    public function isAvailable(): bool
    {
        return true;
    }
}
