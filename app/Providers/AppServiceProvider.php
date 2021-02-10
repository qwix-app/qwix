<?php

namespace App\Providers;

use App\Helpers\MockyApiHelper;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(MockyApiHelper::class, function($app) {
            return new MockyApiHelper();
        });
    }
}
