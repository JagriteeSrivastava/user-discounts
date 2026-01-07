<?php

namespace SmartDiscounts\UserDiscounts;

use Illuminate\Support\ServiceProvider;

class DiscountServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/discounts.php', 'discounts');
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->publishes([
            __DIR__ . '/../config/discounts.php' => config_path('discounts.php'),
        ], 'discounts-config');
    }
}
