<?php

namespace Modules\Auth\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Modules\Auth\Actions\Logout;
use Modules\Auth\Concerns\PasswordValidationRules;

class DeleteUserForm extends Component
{
    use PasswordValidationRules;

    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => $this->currentPasswordRules(),
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }

    public function render()
    {
        return view('auth::settings.delete-user-form');
    }
}
