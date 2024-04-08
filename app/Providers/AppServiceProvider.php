<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Routing\UrlGenerator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(UrlGenerator $url)
    {
        $this->app->bind('path.public', function () {
            return base_path() . config('app.public_path');
        });

        if ($this->app->isProduction()) {
            $url->forceScheme('https');
            // Schema::defaultStringLenght(191);
        }
    }
}
