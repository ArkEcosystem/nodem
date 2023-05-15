<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use ARKEcosystem\Foundation\Fortify\Components\Concerns\InteractsWithUser;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasModal;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class TwoFactorAuthenticationPrompt extends Component
{
    use InteractsWithUser;
    use HasModal;

    /**
     * Mount the Livewire component.
     *
     * @return void
     */
    public function mount() : void
    {
        $this->modalShown = ! (bool) $this->user?->enabledTwoFactor();
    }

    /**
     * Hide the 2FA prompt for the request and save the dismissed state.
     *
     * @return void
     */
    public function dismiss() : void
    {
        $this->closeModal();
    }

    /**
     * Render the component template.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render() : View
    {
        return view('livewire.two-factor-authentication-prompt');
    }
}
