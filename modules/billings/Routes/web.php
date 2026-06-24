<?php

use Illuminate\Support\Facades\Route;
use Modules\Billings\Livewire\BillingList;
use Modules\Billings\Livewire\WebhookLogList;

Route::middleware(['web', 'auth'])->group(function () {
    Route::livewire('billings', BillingList::class)->name('billings.index');
    Route::livewire('webhook-logs', WebhookLogList::class)->name('webhook-logs.index');
});
