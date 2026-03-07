<?php

namespace App\Providers;

use App\Models\PaymentSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // You may bind interfaces to implementations here.
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Use Bootstrap 5 pagination views
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        // Apply payment settings from DB to config (for front store Paystack/Hubtel)
        if (Schema::hasTable('payment_settings')) {
            try {
                PaymentSetting::applyToConfig();
            } catch (\Throwable $e) {
                // Ignore if table empty or not yet migrated
            }
        }
    }
}