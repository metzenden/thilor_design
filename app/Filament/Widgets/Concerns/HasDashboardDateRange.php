<?php

namespace App\Filament\Widgets\Concerns;

use Illuminate\Support\Carbon;

/**
 * Lit la tranche de date choisie dans le filtre du tableau de bord
 * (App\Filament\Pages\Dashboard) et retombe sur les 30 derniers jours
 * si aucune date n'est sélectionnée.
 */
trait HasDashboardDateRange
{
    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function resolveDateRange(): array
    {
        $start = $this->filters['startDate'] ?? null;
        $end = $this->filters['endDate'] ?? null;

        $start = $start ? Carbon::parse($start)->startOfDay() : now()->subDays(29)->startOfDay();
        $end = $end ? Carbon::parse($end)->endOfDay() : now()->endOfDay();

        if ($start->gt($end)) {
            [$start, $end] = [$end, $start];
        }

        // Borne large pour éviter une boucle jour-par-jour démesurée côté graphique.
        if ($start->diffInDays($end) > 366) {
            $start = $end->copy()->subDays(366)->startOfDay();
        }

        return [$start, $end];
    }
}
