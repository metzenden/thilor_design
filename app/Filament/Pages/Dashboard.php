<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('startDate')
                    ->label('Du')
                    ->native(false)
                    ->default(now()->subDays(29))
                    ->maxDate(fn ($get) => $get('endDate') ?? now()),
                DatePicker::make('endDate')
                    ->label('Au')
                    ->native(false)
                    ->default(now())
                    ->minDate(fn ($get) => $get('startDate'))
                    ->maxDate(now()),
            ])
            ->columns(2);
    }
}
