<?php

namespace App\Providers;

use App\Integrations\Przelewy24;
use App\Services\SettingService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Przelewy24::class, function (Application $app) {
            return new Przelewy24();
        });
        $this->app->singleton(SettingService::class, function (Application $app) {
            return new SettingService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Paginator::useBootstrapFive();
        Paginator::defaultView('vendor/pagination/shop-bootstrap-5');
    }
}
