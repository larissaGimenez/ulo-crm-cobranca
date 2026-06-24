<?php

use Illuminate\Support\Facades\Route;
use Modules\Billings\Http\Controllers\OmieWebhookController;

Route::prefix('api')->middleware('api')->group(function () {
    Route::post('webhooks/omie', [OmieWebhookController::class, 'handle'])->name('webhooks.omie');
});
