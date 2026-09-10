<?php

namespace App\Filament\Widgets;

use App\Models\ProductVariant;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockProducts extends BaseWidget
{
    protected static ?string $heading = 'Variantes en rupture ou stock faible';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ProductVariant::query()
                    ->with('product')
                    ->where('stock', '<=', 5)
                    ->where('is_active', true)
                    ->orderBy('stock')
            )
            ->columns([
                Tables\Columns\TextColumn::make('product.name')->label('Produit'),
                Tables\Columns\TextColumn::make('sku')->label('SKU'),
                Tables\Columns\TextColumn::make('size')->label('Taille'),
                Tables\Columns\TextColumn::make('color')->label('Couleur'),
                Tables\Columns\TextColumn::make('stock')
                    ->badge()
                    ->color(fn ($state) => $state == 0 ? 'danger' : 'warning'),
            ])
            ->paginated([5, 10, 25]);
    }
}
