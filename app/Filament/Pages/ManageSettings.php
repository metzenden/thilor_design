<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $navigationLabel = 'Paramètres boutique';

    protected static ?string $slug = 'settings';

    protected static string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->can('settings.manage') ?? false;
    }

    public function mount(): void
    {
        $this->form->fill([
            'shop_name' => Setting::get('shop_name'),
            'shop_tagline' => Setting::get('shop_tagline'),
            'shop_phone' => Setting::get('shop_phone'),
            'shop_email' => Setting::get('shop_email'),
            'shop_address' => Setting::get('shop_address'),
            'social_facebook' => Setting::get('social_facebook'),
            'social_instagram' => Setting::get('social_instagram'),
            'social_tiktok' => Setting::get('social_tiktok'),
            'social_youtube' => Setting::get('social_youtube'),
            'seo_default_title' => Setting::get('seo_default_title'),
            'seo_default_description' => Setting::get('seo_default_description'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Coordonnées de la boutique')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('shop_name')->label('Nom de la boutique')->required(),
                        Forms\Components\TextInput::make('shop_tagline')->label('Slogan'),
                        Forms\Components\TextInput::make('shop_phone')->label('Téléphone'),
                        Forms\Components\TextInput::make('shop_email')->label('Email')->email(),
                        Forms\Components\TextInput::make('shop_address')->label('Adresse')->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Réseaux sociaux')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('social_facebook')->label('Facebook'),
                        Forms\Components\TextInput::make('social_instagram')->label('Instagram'),
                        Forms\Components\TextInput::make('social_tiktok')->label('TikTok'),
                        Forms\Components\TextInput::make('social_youtube')->label('YouTube'),
                    ]),
                Forms\Components\Section::make('SEO par défaut')
                    ->schema([
                        Forms\Components\TextInput::make('seo_default_title')->label('Title par défaut'),
                        Forms\Components\Textarea::make('seo_default_description')->label('Meta description par défaut'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        foreach ($this->form->getState() as $key => $value) {
            Setting::set($key, $value);
        }

        Notification::make()
            ->title('Paramètres enregistrés')
            ->success()
            ->send();
    }
}
