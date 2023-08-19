<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class GlobalServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('global_value', function ($app) {
            return new SessionManager($app);
        });
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
