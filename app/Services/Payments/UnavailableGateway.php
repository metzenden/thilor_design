<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayContract;
use App\Models\Order;
use App\Models\Payment;
use RuntimeException;

/**
 * Squelette pour carte bancaire / Wave / Orange Money : l'architecture est prête
 * (contrat commun, statuts distincts, back-office) mais l'intégration réelle attend
 * les identifiants marchands du client. Ne simule jamais un paiement confirmé.
 */
class UnavailableGateway implements PaymentGatewayContract
{
    public function __construct(
        private readonly string $gatewayCode,
        private readonly string $gatewayLabel,
    ) {}

    public function code(): string
    {
        return $this->gatewayCode;
    }

    public function label(): string
    {
        return $this->gatewayLabel;
    }

    public function initiate(Order $order): Payment
    {
        throw new RuntimeException("Le moyen de paiement « {$this->gatewayLabel} » n'est pas encore configuré (identifiants marchands manquants).");
    }

    public function isAvailable(): bool
    {
        return false;
    }
}
