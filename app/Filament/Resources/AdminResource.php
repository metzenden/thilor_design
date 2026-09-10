<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AdminResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $navigationLabel = 'Administrateurs';

    protected static ?string $slug = 'admins';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('admins.manage') ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->role(['admin', 'manager']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('Nom')->required(),
            Forms\Components\TextInput::make('email')->email()->required(),
            Forms\Components\TextInput::make('phone')->label('Téléphone'),
            Forms\Components\Select::make('roles')
                ->relationship('roles', 'name')
                ->options(['admin' => 'Administrateur', 'manager' => 'Gestionnaire'])
                ->multiple()
                ->required(),
            Forms\Components\TextInput::make('password')
                ->password()
                ->label('Mot de passe')
                ->dehydrateStateUsing(fn ($state) => bcrypt($state))
                ->dehydrated(fn ($state) => filled($state))
                ->required(fn (string $context) => $context === 'create'),
            Forms\Components\Toggle::make('is_active')->label('Actif')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nom')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('roles.name')->label('Rôle')->badge(),
                Tables\Columns\IconColumn::make('is_active')->label('Actif')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (User $record) => $record->id !== auth()->id()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdmins::route('/'),
            'create' => Pages\CreateAdmin::route('/create'),
            'edit' => Pages\EditAdmin::route('/{record}/edit'),
        ];
    }
}
