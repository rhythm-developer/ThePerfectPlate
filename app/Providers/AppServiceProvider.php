<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\OrderEventMenuItem;
use App\Observers\OrderEventMenuItemObserver;
use App\Observers\OrderObserver;
use Filament\Actions\CreateAction;
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

        // "New User" instead of Filament's default "New user".
        CreateAction::configureUsing(
            fn (CreateAction $action) => $action->label(
                fn () => __('filament-actions::create.single.label', ['label' => $action->getTitleCaseModelLabel()])
            )
        );
    }
}
