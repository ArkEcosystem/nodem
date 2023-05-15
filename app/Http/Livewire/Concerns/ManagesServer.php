<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use App\Enums\ServerProcessTypeEnum;
use App\Enums\ServerProviderTypeEnum;
use App\Http\Livewire\EditServerModal;
use App\Jobs\CheckServerCredentials;
use App\Models\Server;
use App\Rules\Server as ServerRules;
use App\Services\Client\RPC;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;

trait ManagesServer
{
    public array $state = [
        'provider'              => null,
        'name'                  => null,
        'host'                  => null,
        'process_type'          => null,
        'auth_username'         => null,
        'auth_password'         => null,
        'auth_access_key'       => null,
        'uses_bip38_encryption' => false,
    ];

    public array $providers = [];

    public array $serverProcessTypes = [];

    public bool $useCredentials = false;

    public bool $serverCheckingError = false;

    public ?string $serverCheckingErrorMessage = null;

    public function updated(string $propertyName): void
    {
        $this->validateRequest($propertyName);
    }

    public function mount(): void
    {
        $this->initForm();
    }

    public function initForm(): void
    {
        $this->setProviders();
        $this->setServerProcessTypes();
    }

    public function onModalClosed(): void
    {
        $this->reset();

        $this->resetValidation();

        $this->initForm();
    }

    private function setProviders(): void
    {
        $this->providers = ServerProviderTypeEnum::toArray();
    }

    private function setServerProcessTypes(): void
    {
        $this->serverProcessTypes = ServerProcessTypeEnum::toArray();
    }

    private function processTypeIsInLine(Server $server): bool
    {
        $processType = $this->state['process_type'];

        try {
            $processList = RPC::fromServer($server)->process()->list();
        } catch (Throwable $e) {
            return false;
        }

        $hasCoreProcess = collect($processList)
            ->some(function (array $process): bool {
                if ($process['status'] !== 'online') {
                    return false;
                }

                return str_ends_with($process['name'], 'core')
                    || str_ends_with($process['name'], 'relay')
                    || str_ends_with($process['name'], 'forger');
            });

        if (! $hasCoreProcess) {
            return true;
        }

        $hasProcessForSelectedType = collect($processList)
            ->some(function ($process) use ($processType) : bool {
                if ($process['status'] !== 'online') {
                    return false;
                }

                if ($processType === ServerProcessTypeEnum::SEPARATE) {
                    return str_ends_with($process['name'], 'relay') || str_ends_with($process['name'], 'forger');
                }

                return str_ends_with($process['name'], 'core');
            });

        if ($hasProcessForSelectedType) {
            return true;
        }

        return false;
    }

    private function serverIsOnline(Server $server): bool
    {
        try {
            Http::timeout(5)->get($server->host);
        } catch (Throwable $e) {
            return false;
        }

        return true;
    }

    private function serverCredentialsAreCorrect(Server $server): bool
    {
        try {
            (new CheckServerCredentials($server))->handle();
        } catch (Throwable $e) {
            return false;
        }

        return true;
    }

    private function validateRequest(string $propertyName = ''): array
    {
        $server = null;
        if ($this instanceof EditServerModal) {
            $server = $this->server;
        }

        $rules = [
            'state.provider'              => ServerRules::provider(['required']),
            'state.name'                  => ServerRules::name(['required']),
            'state.host'                  => ServerRules::host(['required'], $server),
            'state.process_type'          => ServerRules::processType(['required']),
            'state.auth_username'         => ServerRules::authUsername([Rule::requiredIf($this->useCredentials)]),
            'state.auth_password'         => ServerRules::authPassword([Rule::requiredIf($this->useCredentials)]),
            'state.auth_access_key'       => ServerRules::authAccessKey([Rule::requiredIf(! $this->useCredentials)]),
            'state.uses_bip38_encryption' => ServerRules::bip38(),
        ];

        $values = ['state' => $this->state];
        if ($propertyName === 'state.host') {
            $values['state']['host'] = rtrim($values['state']['host'], '/');
        }

        if ($propertyName !== '' && array_key_exists($propertyName, $rules)) {
            $property = explode('.', $propertyName)[1];
            $rules    = [$propertyName => $rules[$propertyName]];
            $values   = [
                'state' => [
                    $property => $values['state'][$property],
                ],
            ];

            $this->resetErrorBag($propertyName);
        } else {
            $this->resetErrorBag();
        }

        $validator = Validator::make($values, $rules);

        if ($validator->fails()) {
            $this->setErrorBag(
                $validator->getMessageBag()->merge($this->getErrorBag())
            );
        }

        return $validator->validated();
    }
}
