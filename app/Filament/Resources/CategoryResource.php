<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Catalogue';

    protected static ?string $navigationLabel = 'Catégories';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('categories.manage') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (string $state, callable $set) => $set('slug', Str::slug($state))),
                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true),
                    Forms\Components\Select::make('parent_id')
                        ->label('Catégorie parente')
                        ->relationship('parent', 'name', fn ($query, $record) => $record ? $query->whereKeyNot($record->id) : $query)
                        ->searchable(),
                    Forms\Components\TextInput::make('position')
                        ->numeric()
                        ->default(0),
                    Forms\Components\FileUpload::make('image')
                        ->image()
                        ->directory('categories')
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('description')
                        ->columnSpanFull(),
                    Forms\Components\Toggle::make('is_active')
                        ->default(true),
                ]),
            Forms\Components\Section::make('SEO')
                ->collapsed()
                ->schema([
                    Forms\Components\TextInput::make('meta_title'),
                    Forms\Components\Textarea::make('meta_description'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label(''),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('parent.name')->label('Parent'),
                Tables\Columns\TextColumn::make('products_count')->counts('products')->label('Produits'),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Actif'),
                Tables\Columns\TextColumn::make('position')->sortable(),
            ])
            ->defaultSort('position')
            ->reorderable('position')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
