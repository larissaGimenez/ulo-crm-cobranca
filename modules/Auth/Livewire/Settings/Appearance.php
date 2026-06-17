<?php

namespace Modules\Auth\Livewire\Settings;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Configurações de aparência')]
class Appearance extends Component
{
    public function render()
    {
        return view('auth::settings.appearance');
    }
}
