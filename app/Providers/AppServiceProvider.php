<?php

namespace App\Providers;

use App\Models\Category;
use App\Services\CartService;
use App\Services\Payments\PaymentGatewayManager;
use Illuminate\Auth\Events\Login;
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

        View::composer(['components.site-header', 'components.site-footer'], function ($view) {
            $view->with('navCategories', Cache::remember(
                'nav.categories',
                now()->addHour(),
                fn () => Category::where('is_active', true)->whereNull('parent_id')->orderBy('position')->get()
            ));
            $view->with('headerCartCount', app(CartService::class)->itemsCount());
        });
    }
}
