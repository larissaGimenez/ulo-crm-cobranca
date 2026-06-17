<?php

use App\Providers\AppServiceProvider;
use App\Providers\FortifyServiceProvider;
use Modules\Clients\Providers\ClientsModuleServiceProvider;

return [
    AppServiceProvider::class,
    FortifyServiceProvider::class,
    ClientsModuleServiceProvider::class
];
