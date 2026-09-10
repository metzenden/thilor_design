<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrdersOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $ordersThisMonth = Order::whereMonth('placed_at', now()->month)->whereYear('placed_at', now()->year);
        $revenueThisMonth = (clone $ordersThisMonth)->whereIn('status', ['processing', 'shipped', 'delivered'])->sum('total');
        $productsSold = OrderItem::whereHas('order', fn ($q) => $q->whereIn('status', ['processing', 'shipped', 'delivered']))->sum('quantity');
        $outOfStock = Product::whereDoesntHave('variants', fn ($q) => $q->where('stock', '>', 0))->count();

        return [
            Stat::make('Commandes ce mois', $ordersThisMonth->count())
                ->color('warning'),
            Stat::make('Chiffre d\'affaires ce mois', number_format($revenueThisMonth, 0, ',', ' ').' FCFA')
                ->color('success'),
            Stat::make('Clients', User::role('client')->count())
                ->color('primary'),
            Stat::make('Produits vendus (total)', $productsSold)
                ->color('primary'),
            Stat::make('Produits en rupture', $outOfStock)
                ->color($outOfStock > 0 ? 'danger' : 'success'),
        ];
    }
}
