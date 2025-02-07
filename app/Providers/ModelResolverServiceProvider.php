<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ModelResolverService;

class ModelResolverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ModelResolverService::class, function ($app) {
            return new ModelResolverService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
