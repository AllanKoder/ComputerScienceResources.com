<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'resource' => \App\Models\ComputerScienceResource::class,
            'review' => \App\Models\ResourceReview::class,
            'comment' => \App\Models\Comment::class,
            'edit' => \App\Models\ResourceEdits::class,
            'user' => \App\Models\User::class,
            // Add other model types here
        ]);
    }
}
