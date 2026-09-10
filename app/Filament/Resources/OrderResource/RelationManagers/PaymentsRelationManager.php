<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = 'Paiements';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('method')
                ->options([
                    'cash_on_delivery' => 'Paiement à la livraison',
                    'card' => 'Carte bancaire',
                    'wave' => 'Wave',
                    'orange_money' => 'Orange Money',
                ])
                ->required(),
            Forms\Components\Select::make('status')
                ->options([
                    'pending' => 'En attente',
                    'paid' => 'Payé',
                    'failed' => 'Échoué',
                    'cancelled' => 'Annulé',
                    'refunded' => 'Remboursé',
                ])
                ->required(),
            Forms\Components\TextInput::make('amount')->numeric()->required(),
            Forms\Components\TextInput::make('transaction_reference')->label('Référence transaction'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('method')
            ->columns([
                Tables\Columns\TextColumn::make('method')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'cash_on_delivery' => 'Paiement à la livraison',
                        'card' => 'Carte bancaire',
                        'wave' => 'Wave',
                        'orange_money' => 'Orange Money',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('amount')->money('XOF', divideBy: 1),
                Tables\Columns\TextColumn::make('paid_at')->dateTime('d/m/Y H:i'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }
}
