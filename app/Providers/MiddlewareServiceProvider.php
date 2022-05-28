<?php

namespace App\Providers;

use App\Modules\Tenant\Http\Middleware\Tenant;
use App\Modules\Tenant\Providers\TenantProvider;
use Illuminate\Support\ServiceProvider;

class MiddlewareServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Tenant::class, function ($app) {
            return new Tenant($app->config->get('tenant.tenant_host', 'localhost'), $app->get(TenantProvider::class));
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
