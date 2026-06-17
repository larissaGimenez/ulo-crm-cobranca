<?php

use App\Providers\AppServiceProvider;
use Modules\Auth\Providers\AuthModuleServiceProvider;
use Modules\Clients\Providers\ClientsModuleServiceProvider;

return [
    AppServiceProvider::class,
    AuthModuleServiceProvider::class,
    ClientsModuleServiceProvider::class
];
