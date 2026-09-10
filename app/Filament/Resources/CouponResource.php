<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?string $navigationLabel = 'Coupons';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('marketing.manage') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('code')
                ->required()
                ->unique(ignoreRecord: true)
                ->formatStateUsing(fn (?string $state) => $state ? strtoupper($state) : $state)
                ->dehydrateStateUsing(fn (string $state) => strtoupper($state)),
            Forms\Components\Select::make('type')
                ->options(['percent' => 'Pourcentage (%)', 'fixed' => 'Montant fixe (FCFA)'])
                ->required()
                ->live(),
            Forms\Components\TextInput::make('value')
                ->label(fn (callable $get) => $get('type') === 'percent' ? 'Valeur (%)' : 'Valeur (FCFA)')
                ->numeric()
                ->required(),
            Forms\Components\TextInput::make('min_amount')
                ->label('Montant minimum de commande (FCFA)')
                ->numeric(),
            Forms\Components\DateTimePicker::make('starts_at')->label('Début'),
            Forms\Components\DateTimePicker::make('ends_at')->label('Fin'),
            Forms\Components\TextInput::make('usage_limit')->label("Limite d'utilisation")->numeric(),
            Forms\Components\Toggle::make('is_active')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->formatStateUsing(fn (string $state) => $state === 'percent' ? '%' : 'FCFA'),
                Tables\Columns\TextColumn::make('value'),
                Tables\Columns\TextColumn::make('used_count')->label('Utilisations'),
                Tables\Columns\TextColumn::make('ends_at')->label('Expire le')->date('d/m/Y'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
