<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Enums\TeamMemberPermission;
use App\Jobs\PingServer;
use App\Jobs\UpdateServer;
use App\Models\Server;
use App\Rules\ValidJsonFile;
use App\ViewModels\ServerViewModel;
use App\ViewModels\ViewModelFactory;
use ARKEcosystem\Foundation\Fortify\Components\Concerns\InteractsWithUser;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\TemporaryUploadedFile;
use Livewire\WithFileUploads;

/**
 * @property \App\Models\User $user
 */
final class ImportServers extends Component
{
    use AuthorizesRequests;
    use InteractsWithUser;
    use WithFileUploads;

    public int $currentStep = 1;

    public TemporaryUploadedFile|string|null $jsonFile = null;

    public ?string $filename = null;

    public array $servers = [];

    public array $selectedServers = [];

    public function mount(): void
    {
        $this->authorize(TeamMemberPermission::SERVER_CONFIGURATION_IMPORT);
    }

    final public function render(): View
    {
        return view('livewire.import-servers');
    }

    public function updatedJsonFile(): void
    {
        /** @var TemporaryUploadedFile $jsonFile */
        $jsonFile = $this->jsonFile;

        $this->filename = $jsonFile->getClientOriginalName();

        $this->validateJsonFile();
    }

    public function validateJsonFile(): void
    {
        $validator = Validator::make([
            'jsonFile' => $this->jsonFile,
        ], $this->jsonFileValidators());

        if ($validator->fails()) {
            $this->dispatchBrowserEvent('validation-error');
        }

        $validator->validate();

        $this->importServersFromJsonFile();

        $this->currentStep = 2;
    }

    public function jsonFileValidators(): array
    {
        return [
            'jsonFile' => [new ValidJsonFile(), 'mimetypes:application/json'],
        ];
    }

    public function removeJsonFile(): void
    {
        $this->jsonFile = null;
        $this->filename = null;
    }

    public function importServersFromJsonFile(): void
    {
        /** @var TemporaryUploadedFile $jsonFile */
        $jsonFile = $this->jsonFile;

        $encoded = $jsonFile->get();
        $content = $encoded === false ? '' : json_decode($encoded);
        $owner   = $this->user->owners->first() ?? $this->user;

        foreach ($content as $server) {
            $server->host = rtrim($server->host, '/');

            $exists = $this->hostAlreadyExists($server->host);

            array_push($this->servers, [
                'user_id'               => $owner->id,
                'provider'              => $server->provider,
                'process_type'          => $server->process_type,
                'uses_bip38_encryption' => $server->uses_bip38_encryption,
                'name'                  => $server->name,
                'host'                  => $server->host,
                'auth_username'         => $server->auth->username ?? null,
                'auth_password'         => $server->auth->password ?? null,
                'auth_access_key'       => $server->auth->access_key ?? null,
                'updated_at'            => now(),
                'exists'                => $exists,
            ]);

            if (! $exists) {
                PingServer::dispatch($server->host);
            }
        }
    }

    public function hostAlreadyExists(string $host): bool
    {
        // Is already on the list of servers?
        if (collect($this->servers)->some(fn (array $server) => $server['host'] === $host)) {
            return true;
        }

        // Is already in the database?
        return $this->user->servers()->whereHost($host)->exists();
    }

    public function updatePingState(): void
    {
        $this->servers = array_map(function (array $server) {
            // Will force the server view model collection to refresh
            $server['updated_at'] = now();

            return $server;
        }, $this->servers);
    }

    public function retry(): void
    {
        $this->serversWithError()->each(function (ServerViewModel $server): void {
            PingServer::dispatch($server->host());
        });
    }

    public function getTemporaryServersProperty(): Collection
    {
        return ViewModelFactory::collection(
            collect($this->servers)
                ->transform(function ($data): Server {
                    // The following removed attributes are only used on the
                    // component, no need to pass them to the view model.
                    unset($data['exists'], $data['updated_at']);

                    return new Server($data);
                })
        );
    }

    public function getHasServersWithErrorProperty(): bool
    {
        return $this->serversWithError()->count() > 0;
    }

    public function getHasPendingServersProperty(): bool
    {
        return collect($this->getTemporaryServersProperty())
            ->some
            ->pingIsPending();
    }

    public function getTemporarySelectedServersProperty(): array
    {
        return $this->getTemporaryServersProperty()
            ->filter(fn (ServerViewModel $server, int $index) => $this->isSelected($index))
            ->all();
    }

    public function availableServers(): Collection
    {
        return $this
            ->getTemporaryServersProperty()
            ->filter(fn (ServerViewModel $server, $index) => ! $this->servers[$index]['exists'] && $server->pingSucceed());
    }

    public function toggleAllServers(): void
    {
        if ($this->hasAllServersSelected()) {
            $this->selectedServers = [];
        } else {
            $this->selectedServers = $this
                ->availableServers()
                ->keys()
                ->toArray();
        }
    }

    public function hasAllServersSelected(): bool
    {
        return $this->availableServers()->count() > 0 && $this->availableServers()->count() === count($this->selectedServers);
    }

    public function selectServer(int $index): void
    {
        if ($this->isSelected($index)) {
            $this->selectedServers = array_diff($this->selectedServers, [$index]);
        } else {
            array_push($this->selectedServers, $index);
        }
    }

    public function isSelected(int $index): bool
    {
        return in_array($index, $this->selectedServers, true);
    }

    public function resetWizard(): void
    {
        $this->reset();
        $this->resetErrorBag();
        $this->resetValidation();
        $this->dispatchBrowserEvent('reset-wizard');
    }

    public function goToPreviousStep(): void
    {
        $this->currentStep -= 1;
    }

    public function goToNextStep(): void
    {
        $this->currentStep += 1;

        if ($this->currentStep === 3) {
            $this->save();
        }
    }

    public function redirectHome(): void
    {
        $this->redirect(route('home'));
    }

    private function serversWithError(): Collection
    {
        return collect($this->getTemporaryServersProperty())
            ->filter(fn (ServerViewModel $server, $index) => ! $this->servers[$index]['exists'] && $server->pingFailed());
    }

    private function save(): void
    {
        foreach ($this->getTemporarySelectedServersProperty() as $server) {
            $model = $server->model();

            $model->save();

            $model->refresh();

            UpdateServer::dispatchSync($this->user, $model);
        }
    }
}
