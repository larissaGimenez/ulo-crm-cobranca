<?php

namespace Modules\Clients\Providers;

use Illuminate\Support\ServiceProvider;

class ClientsModuleServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            \Modules\Clients\Contracts\ClientServiceInterface::class,
            \Modules\Clients\Services\ClientService::class
        );
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}