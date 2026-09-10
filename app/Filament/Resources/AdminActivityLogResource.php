<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminActivityLogResource\Pages;
use App\Models\AdminActivityLog;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AdminActivityLogResource extends Resource
{
    protected static ?string $model = AdminActivityLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $navigationLabel = "Journal d'activité";

    protected static ?string $slug = 'activity-log';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('admins.manage') ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Date')->dateTime('d/m/Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Administrateur'),
                Tables\Columns\TextColumn::make('action')->badge(),
                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Élément')
                    ->formatStateUsing(fn (?string $state) => $state ? class_basename($state) : '—'),
                Tables\Columns\TextColumn::make('subject_id')->label('ID'),
                Tables\Columns\TextColumn::make('ip_address')->label('IP'),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdminActivityLogs::route('/'),
        ];
    }
}
