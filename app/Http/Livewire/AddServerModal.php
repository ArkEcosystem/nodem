<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Enums\ServerProcessTypeEnum;
use App\Exceptions\ServerPreferenceMismatchException;
use App\Http\Livewire\Concerns\ManagesServer;
use App\Jobs\UpdateServer;
use App\Models\Server;
use App\Models\User;
use App\Services\Client\Exceptions\RPCResponseException;
use ARKEcosystem\Foundation\Fortify\Components\Concerns\InteractsWithUser;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasModal;
use Illuminate\Http\Client\ConnectionException;
use Livewire\Component;

final class AddServerModal extends Component
{
    use HasModal;
    use InteractsWithUser;
    use ManagesServer;

    /** @var mixed */
    protected $listeners = [
        'showAddServerModal' => 'openModal',
        'modalClosed'        => 'onModalClosed',
        'credentialsModeChanged',
    ];

    public function addServer(): void
    {
        try {
            $this->validateRequest();

            $this->serverCheckingError = false;

            $server = $this->makeServer($this->state);

            if (! $this->serverIsOnline($server)) {
                throw new RPCResponseException();
            }

            if (! $this->serverCredentialsAreCorrect($server)) {
                throw new RPCResponseException();
            }

            if (! $this->processTypeIsInLine($server)) {
                throw new ServerPreferenceMismatchException();
            }

            /** @var User $user */
            $user = $this->user;

            /** @var Server $server */
            $server = $user->servers()->save($server);

            UpdateServer::dispatchSync($user, $server);

            $this->closeModal();

            $this->emit('serverAdded');
        } catch (RPCResponseException | ConnectionException) {
            $this->serverCheckingError        = true;
            $this->serverCheckingErrorMessage = null;
        } catch (ServerPreferenceMismatchException) {
            $this->serverCheckingError        = true;
            $this->serverCheckingErrorMessage = trans('pages.add-server-modal.process_type.separate_server_error');
            if ($this->state['process_type'] === ServerProcessTypeEnum::COMBINED) {
                $this->serverCheckingErrorMessage = trans('pages.add-server-modal.process_type.combined_server_error');
            }
        }
    }

    public function canSubmit(): bool
    {
        $requiredProperties = [
            'state.provider',
            'state.name',
            'state.host',
            'state.process_type',
        ];

        foreach ($requiredProperties as $property) {
            if (! (bool) data_get($this, $property)) {
                return false;
            }
        }

        if (! $this->useCredentials && ! (bool) data_get($this, 'state.auth_access_key')) {
            return false;
        }

        if ($this->useCredentials && (! (bool) data_get($this, 'state.auth_username') || ! (bool) data_get($this, 'state.auth_password'))
        ) {
            return false;
        }

        return $this->getErrorBag()->count() === 0;
    }

    public function credentialsModeChanged(): void
    {
        $this->state['auth_username']   = null;
        $this->state['auth_password']   = null;
        $this->state['auth_access_key'] = null;
    }

    private function makeServer(array $data): Server
    {
        $data['host'] = rtrim($data['host'], '/');

        return new Server($data);
    }
}
