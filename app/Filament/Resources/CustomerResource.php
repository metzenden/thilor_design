<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CustomerResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Ventes';

    protected static ?string $navigationLabel = 'Clients';

    protected static ?string $slug = 'customers';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('customers.manage') ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->role('client')->withCount('orders');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('Nom')->required(),
            Forms\Components\TextInput::make('email')->email()->required(),
            Forms\Components\TextInput::make('phone')->label('Téléphone'),
            Forms\Components\Toggle::make('is_active')->label('Actif')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nom')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('phone')->label('Téléphone'),
                Tables\Columns\TextColumn::make('orders_count')->label('Commandes'),
                Tables\Columns\TextColumn::make('created_at')->label('Inscrit le')->date('d/m/Y'),
                Tables\Columns\IconColumn::make('is_active')->label('Actif')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\OrdersRelationManager::class,
            RelationManagers\AddressesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
