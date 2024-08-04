<?php

namespace App\Providers;

use App\Services\DiffService;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;

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
    public function boot()
    {
        //
    }
}
