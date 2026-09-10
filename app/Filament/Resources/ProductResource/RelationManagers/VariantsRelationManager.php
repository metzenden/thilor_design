<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $title = 'Variantes (tailles / couleurs / stock)';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('size')
                ->label('Taille'),
            Forms\Components\TextInput::make('color')
                ->label('Couleur'),
            Forms\Components\ColorPicker::make('color_hex')
                ->label('Couleur (hex)'),
            Forms\Components\TextInput::make('sku')
                ->label('SKU')
                ->required()
                ->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('price_override')
                ->label('Prix spécifique (FCFA)')
                ->numeric()
                ->helperText('Laisser vide pour utiliser le prix du produit.'),
            Forms\Components\TextInput::make('stock')
                ->required()
                ->numeric()
                ->default(0),
            Forms\Components\Toggle::make('is_active')
                ->default(true),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('sku')
            ->columns([
                Tables\Columns\TextColumn::make('size')->label('Taille'),
                Tables\Columns\TextColumn::make('color')->label('Couleur'),
                Tables\Columns\TextColumn::make('sku')->label('SKU')->searchable(),
                Tables\Columns\TextColumn::make('stock')
                    ->badge()
                    ->color(fn ($state) => $state > 5 ? 'success' : ($state > 0 ? 'warning' : 'danger')),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
