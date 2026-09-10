<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Contenu';

    protected static ?string $navigationLabel = 'Messages de contact';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('content.manage') ?? false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = ContactMessage::where('is_read', false)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('Nom')->disabled(),
            Forms\Components\TextInput::make('email')->disabled(),
            Forms\Components\TextInput::make('phone')->label('Téléphone')->disabled(),
            Forms\Components\TextInput::make('subject')->label('Sujet')->disabled(),
            Forms\Components\Textarea::make('message')->disabled()->columnSpanFull(),
            Forms\Components\Toggle::make('is_read')->label('Lu'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nom')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('subject')->label('Sujet'),
                Tables\Columns\TextColumn::make('created_at')->label('Reçu le')->dateTime('d/m/Y H:i'),
                Tables\Columns\IconColumn::make('is_read')->label('Lu')->boolean(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_read')->label('Lu'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->mutateRecordDataUsing(function (array $data, ContactMessage $record) {
                        $record->update(['is_read' => true]);

                        return $data;
                    }),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
            'create' => Pages\CreateContactMessage::route('/create'),
            'edit' => Pages\EditContactMessage::route('/{record}/edit'),
        ];
    }
}
