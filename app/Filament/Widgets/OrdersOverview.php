<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDashboardDateRange;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrdersOverview extends BaseWidget
{
    use HasDashboardDateRange;
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        [$start, $end] = $this->resolveDateRange();

        $ordersInRange = Order::whereBetween('placed_at', [$start, $end]);
        $revenueInRange = (clone $ordersInRange)->whereIn('status', ['processing', 'shipped', 'delivered'])->sum('total');
        $productsSold = OrderItem::whereHas(
            'order',
            fn ($q) => $q->whereIn('status', ['processing', 'shipped', 'delivered'])->whereBetween('placed_at', [$start, $end])
        )->sum('quantity');
        $newClients = User::role('client')->whereBetween('created_at', [$start, $end])->count();
        $outOfStock = Product::whereDoesntHave('variants', fn ($q) => $q->where('stock', '>', 0))->count();

        return [
            Stat::make('Commandes (période)', $ordersInRange->count())
                ->color('warning'),
            Stat::make('Chiffre d\'affaires (période)', number_format($revenueInRange, 0, ',', ' ').' FCFA')
                ->color('success'),
            Stat::make('Nouveaux clients (période)', $newClients)
                ->color('primary'),
            Stat::make('Produits vendus (période)', $productsSold)
                ->color('primary'),
            Stat::make('Produits en rupture', $outOfStock)
                ->color($outOfStock > 0 ? 'danger' : 'success'),
        ];
    }
}
