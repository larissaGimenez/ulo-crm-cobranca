<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Livewire\Settings\Appearance;
use Modules\Auth\Livewire\Settings\Profile;
use Modules\Auth\Livewire\Settings\Security;

Route::middleware(['web', 'auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::livewire('settings/profile', Profile::class)->name('profile.edit');
});

Route::middleware(['web', 'auth', 'verified'])->group(function () {
    Route::livewire('settings/appearance', Appearance::class)->name('appearance.edit');

    Route::livewire('settings/security', Security::class)
        ->middleware([
            'password.confirm',
        ])
        ->name('security.edit');
});

Route::middleware(['web'])->group(function () {
    Route::get('.well-known/passkey-endpoints', function () {
        return response()->json([
            'enroll' => route('security.edit'),
            'manage' => route('security.edit'),
        ]);
    })->name('well-known.passkeys');
});
