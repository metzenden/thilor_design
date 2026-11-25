<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDashboardDateRange;
use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class RevenueChart extends ChartWidget
{
    use HasDashboardDateRange;
    use InteractsWithPageFilters;

    protected static ?string $heading = "Chiffre d'affaires";

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        [$start, $end] = $this->resolveDateRange();

        $ordersByDay = Order::query()
            ->whereIn('status', ['processing', 'shipped', 'delivered'])
            ->whereBetween('placed_at', [$start, $end])
            ->get(['total', 'placed_at'])
            ->groupBy(fn (Order $order) => $order->placed_at->format('Y-m-d'));

        $labels = [];
        $revenue = [];

        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $key = $cursor->format('Y-m-d');
            $labels[] = $cursor->translatedFormat('d M');
            $revenue[] = (float) ($ordersByDay->get($key)?->sum('total') ?? 0);
            $cursor->addDay();
        }

        return [
            'datasets' => [
                [
                    'label' => "Chiffre d'affaires (FCFA)",
                    'data' => $revenue,
                    'borderColor' => '#3A2A22',
                    'backgroundColor' => 'rgba(58, 42, 34, 0.12)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
