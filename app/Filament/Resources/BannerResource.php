<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages;
use App\Models\Banner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?string $navigationLabel = 'Bannières';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('content.manage') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->label('Titre')->required(),
            Forms\Components\TextInput::make('subtitle')->label('Sous-titre'),
            Forms\Components\FileUpload::make('image')->image()->maxSize(5120)->directory('banners')
                ->saveUploadedFileUsing(fn ($file) => \App\Support\ImageUploader::store($file, 'banners', maxWidth: 2000))
                ->required(),
            Forms\Components\TextInput::make('cta_label')->label('Texte du bouton'),
            Forms\Components\TextInput::make('link')->label('Lien'),
            Forms\Components\Select::make('position_key')
                ->label('Emplacement')
                ->options([
                    'home_hero' => 'Accueil — bannière principale',
                    'home_secondary' => 'Accueil — bannière secondaire',
                ])
                ->required(),
            Forms\Components\TextInput::make('position')->numeric()->default(0),
            Forms\Components\DateTimePicker::make('starts_at')->label('Début'),
            Forms\Components\DateTimePicker::make('ends_at')->label('Fin'),
            Forms\Components\Toggle::make('is_active')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label(''),
                Tables\Columns\TextColumn::make('title')->label('Titre'),
                Tables\Columns\TextColumn::make('position_key')->label('Emplacement'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->defaultSort('position')
            ->reorderable('position')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}
