<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Server;
use ARKEcosystem\Foundation\Fortify\Components\Concerns\InteractsWithUser;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasModal;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

/**
 * @property \App\Models\User $user
 */
final class Bip38PasswordModal extends Component
{
    use AuthorizesRequests;
    use InteractsWithUser;
    use HasModal;

    public ?Server $server = null;

    public ?string $processType = null;

    public ?string $action = null;

    public string $bip38Password = '';

    /** @var array */
    protected $rules = [
        'bip38Password' => ['required', 'string'],
    ];

    /** @var mixed */
    protected $listeners = ['askForBip38Password'];

    public function askForBip38Password(array $data): void
    {
        [$action, $processType, $serverId] = $data;

        $this->action      = $action;
        $this->processType = $processType;
        $this->server      = $this->user->servers()->findOrFail($serverId);

        $this->openModal();
    }

    public function updatedbip38Password(): void
    {
        $this->resetErrorBag();
    }

    public function submit(): void
    {
        $this->validate();

        $this->emit('triggerServerAction', [
            $this->action,
            $this->processType,
            $this->server?->id,
            ['args' => '--password '.escapeshellarg($this->bip38Password)],
        ]);

        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->reset();

        $this->resetErrorBag();

        $this->modalClosed();
    }
}
