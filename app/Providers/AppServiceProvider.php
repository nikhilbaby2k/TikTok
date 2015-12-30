<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Contracts\Repositories\FirebirdRepositoryInterface', 'App\Repositories\FirebirdRepository');
        $this->app->bind('App\Contracts\Repositories\DevRepositoryInterface', 'App\Repositories\DevRepository');
        $this->app->bind('App\Contracts\Services\TikTokServiceInterface', 'App\Services\TikTokService');
    }
}
