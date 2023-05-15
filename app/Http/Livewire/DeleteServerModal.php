<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Enums\TeamMemberPermission;
use App\Models\Server;
use ARKEcosystem\Foundation\Fortify\Components\Concerns\InteractsWithUser;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasModal;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Component;

/**
 * @property \App\Models\User $user
 */
final class DeleteServerModal extends Component
{
    use AuthorizesRequests;
    use HasModal;
    use InteractsWithUser;

    public ?Server $server;

    public ?string $serverNameConfirmation = null;

    /** @var mixed */
    protected $listeners = ['triggerServerDelete' => 'open'];

    public function open(int $id): void
    {
        $this->server = $this->user->servers()->findOrFail($id);

        $this->openModal();
    }

    public function close(): void
    {
        $this->resetErrorBag();

        $this->server                 = null;
        $this->serverNameConfirmation = null;

        $this->closeModal();
    }

    public function updatedServerNameConfirmation(): void
    {
        /** @var string $serverName */
        $serverName = $this->server?->name;

        $this->validate([
            'serverNameConfirmation' => ['present', Rule::in($serverName)],
        ]);
    }

    public function getCanSubmitProperty(): bool
    {
        return ! is_null($this->serverNameConfirmation) && $this->serverNameConfirmation !== '' && $this->getErrorBag()->count() === 0;
    }

    public function deleteServer(): void
    {
        $this->authorize(TeamMemberPermission::SERVER_DELETE);

        if ($this->getCanSubmitProperty()) {
            /** @var Server $server */
            $server = $this->server;

            $server->delete();

            $this->close();

            $this->redirect(route('home'));
        }
    }
}
