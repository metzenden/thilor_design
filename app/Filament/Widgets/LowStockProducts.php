<?php

namespace App\Filament\Widgets;

use App\Models\ProductVariant;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockProducts extends BaseWidget
{
    protected static ?string $heading = 'Variantes en rupture ou stock faible';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ProductVariant::query()
                    ->with('product')
                    ->where('stock', '<=', 5)
                    ->where('is_active', true)
            )
            ->columns([
                Tables\Columns\TextColumn::make('product.name')->label('Produit')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('sku')->label('SKU')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('size')->label('Taille')->sortable(),
                Tables\Columns\TextColumn::make('color')->label('Couleur')->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state == 0 ? 'danger' : 'warning'),
            ])
            ->defaultSort('stock')
            ->paginated([5, 10, 25]);
    }
}
