<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayContract;
use Illuminate\Support\Collection;

class PaymentGatewayManager
{
    /** @var array<string, PaymentGatewayContract> */
    private array $gateways;

    public function __construct()
    {
        $this->gateways = [
            'cash_on_delivery' => new CashOnDeliveryGateway,
            'card' => new UnavailableGateway('card', 'Carte bancaire'),
            'wave' => new UnavailableGateway('wave', 'Wave'),
            'orange_money' => new UnavailableGateway('orange_money', 'Orange Money'),
        ];
    }

    public function get(string $code): PaymentGatewayContract
    {
        return $this->gateways[$code] ?? throw new \InvalidArgumentException("Moyen de paiement inconnu : {$code}");
    }

    /** @return Collection<int, PaymentGatewayContract> */
    public function all(): Collection
    {
        return collect($this->gateways);
    }

    /** @return Collection<int, PaymentGatewayContract> */
    public function available(): Collection
    {
        return $this->all()->filter->isAvailable();
    }
}
