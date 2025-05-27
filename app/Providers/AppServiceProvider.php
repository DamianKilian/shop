<?php

namespace App\Providers;

use App\Payment\PaymentManager;
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
        $this->app->singleton(SettingService::class, function (Application $app) {
            return new SettingService();
        });

        $this->app->singleton(PaymentManager::class, function ($app) {
            $paymentManager = new PaymentManager($app);
            $paymentManager->extend('hotpay', fn() => new \App\Integrations\HotPay());
            $paymentManager->extend('przelewy24', fn() => new \App\Integrations\Przelewy24());
            return $paymentManager;
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
