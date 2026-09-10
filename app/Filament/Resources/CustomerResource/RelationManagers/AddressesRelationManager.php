<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';

    protected static ?string $title = 'Adresses';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('full_name')
            ->columns([
                Tables\Columns\TextColumn::make('full_name')->label('Nom'),
                Tables\Columns\TextColumn::make('phone')->label('Téléphone'),
                Tables\Columns\TextColumn::make('city')->label('Ville'),
                Tables\Columns\IconColumn::make('is_default')->label('Défaut')->boolean(),
            ]);
    }
}
