<?php

namespace Modules\Auth\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Livewire\Livewire;
use Modules\Auth\Actions\Fortify\CreateNewUser;
use Modules\Auth\Actions\Fortify\ResetUserPassword;

class AuthModuleServiceProvider extends ServiceProvider
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
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'auth');

        // Carregar rotas do módulo
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');

        // Configurar Fortify
        $this->configureFortifyActions();
        $this->configureFortifyViews();
        $this->configureFortifyRateLimiting();

        // Registrar componentes Livewire do módulo
        $this->registerLivewireComponents();
    }

    /**
     * Configure Fortify actions.
     */
    private function configureFortifyActions(): void
    {
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::createUsersUsing(CreateNewUser::class);
    }

    /**
     * Configure Fortify views.
     */
    private function configureFortifyViews(): void
    {
        Fortify::loginView(fn () => view('auth::auth.login'));
        Fortify::verifyEmailView(fn () => view('auth::auth.verify-email'));
        Fortify::twoFactorChallengeView(fn () => view('auth::auth.two-factor-challenge'));
        Fortify::confirmPasswordView(fn () => view('auth::auth.confirm-password'));
        Fortify::registerView(fn () => view('auth::auth.register'));
        Fortify::resetPasswordView(fn () => view('auth::auth.reset-password'));
        Fortify::requestPasswordResetLinkView(fn () => view('auth::auth.forgot-password'));
    }

    /**
     * Configure rate limiting.
     */
    private function configureFortifyRateLimiting(): void
    {
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('passkeys', function (Request $request) {
            $credentialId = $request->input('credential.id');

            return Limit::perMinute(10)->by(
                ($credentialId ?: $request->session()->getId()).'|'.$request->ip(),
            );
        });
    }

    /**
     * Registrar componentes Livewire locais.
     */
    private function registerLivewireComponents(): void
    {
        Livewire::component('settings.appearance', \Modules\Auth\Livewire\Settings\Appearance::class);
        Livewire::component('settings.delete-user-form', \Modules\Auth\Livewire\Settings\DeleteUserForm::class);
        Livewire::component('settings.profile', \Modules\Auth\Livewire\Settings\Profile::class);
        Livewire::component('settings.security', \Modules\Auth\Livewire\Settings\Security::class);
        Livewire::component('settings.two-factor.recovery-codes', \Modules\Auth\Livewire\Settings\TwoFactor\RecoveryCodes::class);
    }
}
