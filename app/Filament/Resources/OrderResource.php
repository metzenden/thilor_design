<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\AdminActivityLog;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Ventes';

    protected static ?string $navigationLabel = 'Commandes';

    protected static ?string $recordTitleAttribute = 'order_number';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('orders.manage') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Commande')
                ->columns(3)
                ->schema([
                    Forms\Components\TextInput::make('order_number')->disabled(),
                    Forms\Components\Select::make('status')
                        ->label('Statut')
                        ->options([
                            'pending' => 'En attente',
                            'processing' => 'En préparation',
                            'shipped' => 'Expédiée',
                            'delivered' => 'Livrée',
                            'cancelled' => 'Annulée',
                        ])
                        ->required(),
                    Forms\Components\Select::make('shipping_method_id')
                        ->label('Mode de livraison')
                        ->relationship('shippingMethod', 'name'),
                ]),
            Forms\Components\Section::make('Client')
                ->columns(3)
                ->schema([
                    Forms\Components\TextInput::make('customer_name')->label('Nom')->required(),
                    Forms\Components\TextInput::make('customer_email')->label('Email')->required(),
                    Forms\Components\TextInput::make('customer_phone')->label('Téléphone')->required(),
                ]),
            Forms\Components\Section::make('Adresse de livraison')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('shipping_address_line')->label('Adresse')->columnSpanFull(),
                    Forms\Components\TextInput::make('shipping_city')->label('Ville'),
                    Forms\Components\TextInput::make('shipping_district')->label('Quartier'),
                    Forms\Components\TextInput::make('shipping_postal_code')->label('Code postal'),
                    Forms\Components\TextInput::make('shipping_country')->label('Pays'),
                ]),
            Forms\Components\Section::make('Montants')
                ->columns(4)
                ->schema([
                    Forms\Components\TextInput::make('subtotal')->label('Sous-total')->numeric()->disabled(),
                    Forms\Components\TextInput::make('shipping_cost')->label('Livraison')->numeric()->disabled(),
                    Forms\Components\TextInput::make('discount_amount')->label('Remise')->numeric()->disabled(),
                    Forms\Components\TextInput::make('total')->label('Total')->numeric()->disabled(),
                ]),
            Forms\Components\Textarea::make('notes')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')->label('N° commande')->searchable(),
                Tables\Columns\TextColumn::make('customer_name')->label('Client')->searchable(),
                Tables\Columns\TextColumn::make('placed_at')->label('Date')->dateTime('d/m/Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('total')->money('XOF', divideBy: 1)->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
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
                Tables\Columns\TextColumn::make('payment.status')
                    ->label('Paiement')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'pending' => 'En attente',
                        'paid' => 'Payé',
                        'failed' => 'Échoué',
                        'cancelled' => 'Annulé',
                        'refunded' => 'Remboursé',
                        default => '—',
                    })
                    ->color(fn (?string $state) => match ($state) {
                        'paid' => 'success',
                        'failed', 'cancelled' => 'danger',
                        'refunded' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('placed_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'processing' => 'En préparation',
                        'shipped' => 'Expédiée',
                        'delivered' => 'Livrée',
                        'cancelled' => 'Annulée',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('markPaid')
                    ->label('Marquer payée')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn (Order $record) => $record->payment && $record->payment->status !== 'paid')
                    ->requiresConfirmation()
                    ->action(function (Order $record) {
                        $record->payment?->update(['status' => 'paid', 'paid_at' => now()]);
                        AdminActivityLog::record('order.marked_paid', $record);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
            RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
