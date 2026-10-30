<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\OrderEventMenuItem;
use App\Observers\OrderEventMenuItemObserver;
use App\Observers\OrderObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        OrderEventMenuItem::observe(OrderEventMenuItemObserver::class);
        Order::observe(OrderObserver::class);
    }
}
