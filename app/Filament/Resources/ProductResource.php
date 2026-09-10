<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Catalogue';

    protected static ?string $navigationLabel = 'Produits';

    protected static ?int $navigationSort = 0;

    protected static ?string $recordTitleAttribute = 'name';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('products.manage') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Group::make()
                ->columnSpan(2)
                ->schema([
                    Forms\Components\Section::make('Informations produit')
                        ->columns(2)
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (string $state, callable $set) => $set('slug', Str::slug($state)))
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('slug')
                                ->required()
                                ->unique(ignoreRecord: true),
                            Forms\Components\TextInput::make('sku')
                                ->label('SKU')
                                ->required()
                                ->unique(ignoreRecord: true),
                            Forms\Components\Select::make('category_id')
                                ->label('Catégorie')
                                ->relationship('category', 'name')
                                ->required()
                                ->searchable(),
                            Forms\Components\Select::make('collections')
                                ->relationship('collections', 'name')
                                ->multiple()
                                ->searchable(),
                            Forms\Components\Textarea::make('short_description')
                                ->label('Description courte')
                                ->columnSpanFull(),
                            Forms\Components\RichEditor::make('description')
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('material')
                                ->label('Matière'),
                            Forms\Components\TextInput::make('care_instructions')
                                ->label('Entretien'),
                        ]),
                    Forms\Components\Section::make('Prix')
                        ->columns(2)
                        ->schema([
                            Forms\Components\TextInput::make('price')
                                ->label('Prix (FCFA)')
                                ->required()
                                ->numeric()
                                ->suffix('FCFA'),
                            Forms\Components\TextInput::make('compare_at_price')
                                ->label('Prix barré / promo (FCFA)')
                                ->numeric()
                                ->suffix('FCFA')
                                ->helperText('Laisser vide si pas de promotion. Doit être supérieur au prix.'),
                        ]),
                    Forms\Components\Section::make('Images')
                        ->schema([
                            Forms\Components\Repeater::make('images')
                                ->relationship('images')
                                ->schema([
                                    Forms\Components\FileUpload::make('path')
                                        ->image()
                                        ->directory('products')
                                        ->required(),
                                    Forms\Components\TextInput::make('alt_text')
                                        ->label('Texte alternatif (SEO)')
                                        ->required(),
                                    Forms\Components\Toggle::make('is_primary')
                                        ->label('Image principale'),
                                ])
                                ->columns(3)
                                ->defaultItems(1)
                                ->reorderable('position')
                                ->collapsible(),
                        ]),
                ]),
            Forms\Components\Group::make()
                ->columnSpan(1)
                ->schema([
                    Forms\Components\Section::make('Publication')
                        ->schema([
                            Forms\Components\Toggle::make('is_active')
                                ->label('Actif')
                                ->default(true),
                            Forms\Components\Toggle::make('is_featured')
                                ->label('Mis en avant'),
                            Forms\Components\DateTimePicker::make('published_at')
                                ->default(now()),
                        ]),
                    Forms\Components\Section::make('SEO')
                        ->collapsed()
                        ->schema([
                            Forms\Components\TextInput::make('meta_title'),
                            Forms\Components\Textarea::make('meta_description'),
                        ]),
                ]),
        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('images.path')
                    ->label('')
                    ->limit(1),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Product $record) => $record->sku),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Catégorie')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Prix')
                    ->money('XOF', divideBy: 1)
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_stock')
                    ->label('Stock')
                    ->state(fn (Product $record) => $record->variants->sum('stock'))
                    ->badge()
                    ->color(fn ($state) => $state > 5 ? 'success' : ($state > 0 ? 'warning' : 'danger')),
                Tables\Columns\IconColumn::make('is_active')->label('Actif')->boolean(),
                Tables\Columns\IconColumn::make('is_featured')->label('Vedette')->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Catégorie')
                    ->relationship('category', 'name'),
                Tables\Filters\TernaryFilter::make('is_active')->label('Actif'),
                Tables\Filters\TernaryFilter::make('is_featured')->label('Vedette'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->with(['category', 'variants']);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\VariantsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
