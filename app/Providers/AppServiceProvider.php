<?php

namespace App\Providers;

use App\Models\AdminActivityLog;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Services\CartService;
use App\Services\Payments\PaymentGatewayManager;
use Illuminate\Auth\Events\Login;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CartService::class);
        $this->app->singleton(PaymentGatewayManager::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Fusionne le panier "invité" (session) dans le panier du client à la connexion.
        // Le jeton est lu AVANT tout regenerate() de session déclenché par le contrôleur de login.
        Event::listen(function (Login $event) {
            $guestToken = session('cart_guest_token');
            app(CartService::class)->mergeGuestCartIntoUser($event->user->id, $guestToken);
        });

        $this->logAdminActivityFor([Product::class, Order::class, Coupon::class]);

        View::composer(['components.site-header', 'components.site-footer'], function ($view) {
            $view->with('navCategories', Cache::remember(
                'nav.categories',
                now()->addHour(),
                fn () => Category::where('is_active', true)->whereNull('parent_id')->orderBy('position')->get()
            ));
            $view->with('headerCartCount', app(CartService::class)->itemsCount());
        });
    }

    /**
     * Journalise les actions administratives importantes (créations, modifications,
     * suppressions) sur les entités sensibles, uniquement quand elles sont réalisées
     * par un administrateur/gestionnaire authentifié (pas les commandes clients).
     *
     * @param  array<class-string<Model>>  $models
     */
    private function logAdminActivityFor(array $models): void
    {
        foreach ($models as $modelClass) {
            $modelClass::saved(function (Model $record) {
                $user = auth()->user();
                if ($user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'manager'])) {
                    $action = ($record->wasRecentlyCreated ? 'created' : 'updated');
                    AdminActivityLog::record(class_basename($record).'.'.$action, $record, $record->getChanges());
                }
            });

            $modelClass::deleted(function (Model $record) {
                $user = auth()->user();
                if ($user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'manager'])) {
                    AdminActivityLog::record(class_basename($record).'.deleted', $record);
                }
            });
        }
    }
}
