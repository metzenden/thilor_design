<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDashboardDateRange;
use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentOrders extends BaseWidget
{
    use HasDashboardDateRange;
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Commandes (période filtrée)';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        [$start, $end] = $this->resolveDateRange();

        return $table
            ->query(
                Order::query()->whereBetween('placed_at', [$start, $end])
            )
            ->columns([
                Tables\Columns\TextColumn::make('order_number')->label('N° commande')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('customer_name')->label('Client')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('placed_at')->label('Date')->dateTime('d/m/Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('total')->label('Total')->money('XOF', divideBy: 1)->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'pending' => 'En attente',
                        'processing' => 'En préparation',
                        'shipped' => 'Expédiée',
                        'delivered' => 'Livrée',
                        'cancelled' => 'Annulée',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'pending' => 'gray',
                        'processing' => 'warning',
                        'shipped' => 'info',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('placed_at', 'desc')
            ->paginated([5, 10, 25]);
    }
}
