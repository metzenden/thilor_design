<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationGroup = 'Catalogue';

    protected static ?string $navigationLabel = 'Avis';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('reviews.manage') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('product_id')->relationship('product', 'name')->required(),
            Forms\Components\Select::make('user_id')->relationship('user', 'name')->required(),
            Forms\Components\TextInput::make('rating')->numeric()->minValue(1)->maxValue(5)->required(),
            Forms\Components\TextInput::make('title'),
            Forms\Components\Textarea::make('comment')->columnSpanFull(),
            Forms\Components\Toggle::make('is_approved')->label('Approuvé'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')->label('Produit')->searchable(),
                Tables\Columns\TextColumn::make('user.name')->label('Client'),
                Tables\Columns\TextColumn::make('rating')->label('Note')->formatStateUsing(fn ($state) => str_repeat('★', (int) $state)),
                Tables\Columns\TextColumn::make('comment')->limit(50),
                Tables\Columns\IconColumn::make('is_approved')->label('Approuvé')->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_approved')->label('Approuvé'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approuver')
                    ->icon('heroicon-o-check')
                    ->visible(fn (Review $record) => ! $record->is_approved)
                    ->action(fn (Review $record) => $record->update(['is_approved' => true])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
