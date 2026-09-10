<?php

namespace App\Contracts;

use App\Models\Order;
use App\Models\Payment;

/**
 * Contrat commun à tous les moyens de paiement. Permet d'ajouter carte bancaire,
 * Wave, Orange Money... dès que les identifiants marchands seront fournis, sans
 * modifier le code du checkout (Order/Payment restent la seule source de vérité).
 */
interface PaymentGatewayContract
{
    public function code(): string;

    public function label(): string;

    /**
     * Amorce le paiement pour la commande donnée et retourne le Payment créé.
     * Ne doit JAMAIS marquer un paiement comme "paid" sans confirmation réelle du
     * prestataire — voir CashOnDeliveryGateway pour le seul cas où "pending" est
     * un état final légitime tant que la livraison n'est pas confirmée.
     */
    public function initiate(Order $order): Payment;

    public function isAvailable(): bool;
}
