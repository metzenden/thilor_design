<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Articles commandés';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product_name')
            ->columns([
                Tables\Columns\TextColumn::make('product_name')->label('Produit'),
                Tables\Columns\TextColumn::make('variant_label')->label('Variante'),
                Tables\Columns\TextColumn::make('sku')->label('SKU'),
                Tables\Columns\TextColumn::make('unit_price')->label('Prix unitaire')->money('XOF', divideBy: 1),
                Tables\Columns\TextColumn::make('quantity')->label('Qté'),
                Tables\Columns\TextColumn::make('line_total')->label('Total ligne')->money('XOF', divideBy: 1),
            ]);
    }
}
