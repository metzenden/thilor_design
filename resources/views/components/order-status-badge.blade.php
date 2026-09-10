@props(['status'])

@php
    $labels = [
        'pending' => 'En attente',
        'processing' => 'En préparation',
        'shipped' => 'Expédiée',
        'delivered' => 'Livrée',
        'cancelled' => 'Annulée',
        'paid' => 'Payé',
        'failed' => 'Échoué',
        'refunded' => 'Remboursé',
    ];
    $colors = [
        'pending' => 'bg-ink/10 text-ink/70',
        'processing' => 'bg-amber-100 text-amber-800',
        'shipped' => 'bg-blue-100 text-blue-800',
        'delivered' => 'bg-green-100 text-green-800',
        'cancelled' => 'bg-red-100 text-red-800',
        'paid' => 'bg-green-100 text-green-800',
        'failed' => 'bg-red-100 text-red-800',
        'refunded' => 'bg-amber-100 text-amber-800',
    ];
@endphp

<span class="rounded-full px-3 py-1 text-xs font-semibold {{ $colors[$status] ?? 'bg-ink/10' }}">
    {{ $labels[$status] ?? $status }}
</span>
