<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Enums\ServerProcessTypeEnum;
use App\Enums\TeamMemberPermission;
use App\Http\Livewire\Concerns\ManagesServer;
use App\Jobs\UpdateServer;
use App\Models\Server;
use ARKEcosystem\Foundation\Fortify\Components\Concerns\InteractsWithUser;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasModal;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

/**
 * @property \App\Models\User $user
 */
final class EditServerModal extends Component
{
    use AuthorizesRequests;
    use HasModal;
    use InteractsWithUser;
    use ManagesServer;

    /** @var Server */
    public $server;

    /** @var mixed */
    protected $listeners = [
        'triggerServerEdit'  => 'open',
        'modalClosed'        => 'onModalClosed',
    ];

    public function open(int $id): void
    {
        $this->serverCheckingError = false;
        $this->server              = $this->user->servers()->findOrFail($id);
        $serverData                = $this->server->toArray();
        foreach ($this->state as $key => $value) {
            $this->state[$key] = $serverData[$key];
        }

        if ($this->server->usesBasicAuth()) {
            $this->useCredentials = true;
        }

        $this->openModal();
    }

    public function editServer(): void
    {
        $this->authorize(TeamMemberPermission::SERVER_EDIT);

        $this->validateRequest();

        $stateServer       = new Server($this->state);
        $stateServer->host = rtrim($stateServer->host, '/');
        if (! $this->serverIsOnline($stateServer)) {
            $this->serverCheckingError = true;

            return;
        }

        if (! $this->serverCredentialsAreCorrect($stateServer)) {
            $this->serverCheckingError = true;

            return;
        }

        if (! $this->processTypeIsInLine($stateServer)) {
            $this->serverCheckingError        = true;
            $this->serverCheckingErrorMessage = trans('pages.add-server-modal.process_type.separate_server_error');
            if ($stateServer->process_type === ServerProcessTypeEnum::COMBINED) {
                $this->serverCheckingErrorMessage = trans('pages.add-server-modal.process_type.combined_server_error');
            }

            return;
        }

        $this->server->forceFill($this->state);
        $this->server->host = rtrim($this->server->host, '/');
        $this->server->save();

        UpdateServer::dispatch($this->user, $this->server);

        $this->closeModal();
        $this->onModalClosed();

        $this->redirect(url()->previous());
    }

    public function canSubmit(): bool
    {
        $hasChange  = false;
        $serverData = $this->server->toArray();
        foreach ($this->state as $key => $value) {
            if ($serverData[$key] !== $value) {
                $hasChange = true;

                break;
            }
        }

        if (! $hasChange) {
            return false;
        }

        return $this->getErrorBag()->count() === 0;
    }
}
