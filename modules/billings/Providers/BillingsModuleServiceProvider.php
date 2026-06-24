<?php

namespace Modules\Billings\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class BillingsModuleServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Carregar views do módulo
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'billings');

        // Carregar migrations do módulo
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        // Carregar rotas do módulo
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');

        // Registrar componente Livewire
        Livewire::component('billings.list', \Modules\Billings\Livewire\BillingList::class);
        Livewire::component('billings.webhook-log-list', \Modules\Billings\Livewire\WebhookLogList::class);
    }
}
