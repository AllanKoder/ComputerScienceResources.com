<?php

namespace App\Providers;

use App\Services\DiffService;
use App\Services\ResourceEditService;
use App\Services\ResourceService;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class AppServiceProvider extends ServiceProvider
{
    
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [

    ];


    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [
        DiffService::class => DiffService::class,
        ResourceEditService::class => ResourceEditService::class,
        ResourceService::class => ResourceService::class,
    ];


    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Verify Email Address')
                ->line('Click the button below to verify your email address and get started helping the world with Computer Science Resources.')
                ->action('Verify Email Address', $url);
        });
    }
}
