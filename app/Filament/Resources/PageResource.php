<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Contenu';

    protected static ?string $navigationLabel = 'Pages & Blog';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('content.manage') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('type')
                ->options(['page' => 'Page statique', 'article' => 'Article de blog'])
                ->required()
                ->live(),
            Forms\Components\TextInput::make('title')
                ->label('Titre')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn (string $state, callable $set) => $set('slug', Str::slug($state))),
            Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('excerpt')->label('Extrait')->columnSpanFull(),
            Forms\Components\FileUpload::make('cover_image')->image()->directory('pages')->label('Image de couverture'),
            Forms\Components\RichEditor::make('content')->label('Contenu')->required()->columnSpanFull(),
            Forms\Components\Toggle::make('is_published')->label('Publié')->default(true),
            Forms\Components\DateTimePicker::make('published_at')->default(now()),
            Forms\Components\TextInput::make('meta_title'),
            Forms\Components\Textarea::make('meta_description'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Titre')->searchable(),
                Tables\Columns\TextColumn::make('type')->formatStateUsing(fn ($state) => $state === 'page' ? 'Page' : 'Article'),
                Tables\Columns\IconColumn::make('is_published')->label('Publié')->boolean(),
                Tables\Columns\TextColumn::make('published_at')->label('Publié le')->date('d/m/Y'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')->options(['page' => 'Page', 'article' => 'Article']),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
