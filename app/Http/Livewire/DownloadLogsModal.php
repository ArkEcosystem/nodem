<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Actions\SearchLogs;
use App\Enums\LogLevel;
use App\Http\Livewire\Concerns\InteractsWithLogFilters;
use App\Models\Server;
use App\Rules\DateTimeRules;
use App\Services\Client\Client;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasModal;
use Closure;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

/**
 * @property \App\Models\User $user
 */
final class DownloadLogsModal extends Component
{
    use HasModal;
    use InteractsWithLogFilters;

    public Server $server;

    public bool $passedValidation = false;

    public bool $selectedAll = false;

    public array $state = [
        'dateFrom' => null,
        'dateTo'   => null,
        'timeFrom' => null,
        'timeTo'   => null,
        'levels'   => [],
    ];

    /** @var mixed */
    protected $listeners = [
        'showDownloadLogsModal',
        'modalClosed' => 'resetComponentState',
    ];

    /**
     * When state is changed, make sure to disable the "Select all" checkbox if not all levels are toggled.
     *
     * @return void
     */
    public function updatedState() : void
    {
        if (! $this->hasSelectedAllLevels()) {
            $this->selectedAll = false;
        }

        $this->state['timeFrom'] = $this->formatTimeInputPayload($this->state['timeFrom'] ?? '');
        $this->state['timeTo']   = $this->formatTimeInputPayload($this->state['timeTo'] ?? '');
    }

    /**
     * If the user presses the "Select all" checkbox, we want to toggle all levels.
     *
     * @param bool $value
     *
     * @return void
     */
    public function updatedSelectedAll(bool $value) : void
    {
        $this->state['levels'] = $value ? $this->formatArrayForCheckbox(LogLevel::all()) : [];
    }

    public function showDownloadLogsModal(string $process) : void
    {
        $this->state['process'] = $process;

        $this->resetComponentState();

        $this->openModal();
    }

    public function download() : StreamedResponse|null
    {
        $this->performValidation();

        $this->resetErrorBag();

        if (! $this->passedValidation) {
            $this->passedValidation = true;
            $this->dispatchBrowserEvent('perform-logs-download');

            return null;
        }

        return $this->rescue(function () : StreamedResponse {
            $client = Client::fromServer($this->server);

            ['url' => $url, 'filename' => $filename] = $this->fetchDownloadUrl();

            $this->closeModal();

            return response()->streamDownload(function () use ($client, $url, $filename) : void {
                echo $client->withOptions([
                    'decode_content' => $this->shouldDecodeDownloadContent($filename),
                ])->get($url)->body();
            }, $filename);
        });
    }

    public function resetComponentState() : void
    {
        $this->resetErrorBag();

        $process = $this->state['process'];

        $this->reset([
            'selectedAll', 'state', 'passedValidation',
        ]);

        $this->state['process'] = $process === 'forger' ? 'forger' : 'relay';
        $this->state['levels']  = $this->formatArrayForCheckbox([
            LogLevel::FATAL, LogLevel::ERROR, LogLevel::WARNING, LogLevel::INFO, LogLevel::DEBUG,
        ]);
    }

    public function render() : View
    {
        return view('livewire.download-logs-modal', [
            'levels' => LogLevel::withLabels(),
        ]);
    }

    private function fetchDownloadUrl() : array
    {
        // The `now()` call cannot really happen because these fields are validated...
        // This is just to make PHPStan and coverage happy...

        return SearchLogs::new($this->server)->process($this->state['process'])->time(
            $this->startDate() ?? now(),
            $this->endDate() ?? now()
        )->levels($this->levels())->download();
    }

    /**
     * Handle the unexpected server errors during download.
     *
     * @param \Closure $callback
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    private function rescue(Closure $callback) : StreamedResponse
    {
        try {
            return $callback();
        } catch (Throwable $e) {
            report($e);

            throw ValidationException::withMessages([
                'server' => [trans('pages.download-logs-modal.messages.unexpected')],
            ]);
        }
    }

    private function shouldDecodeDownloadContent(string $filename): bool
    {
        return ! str_ends_with($filename, '.gz');
    }

    private function performValidation() : void
    {
        $this->resetErrorBag();

        validator([
            'state' => $this->state,
        ], $this->validationRules(), $this->messages())->after(function (Validator $validator) : void {
            if (count($this->levels()) === 0) {
                $validator->errors()->add('state.levels', trans('pages.download-logs-modal.messages.no-levels'));
            }

            $this->validateDates($validator);
        })->validate();
    }

    private function validationRules() : array
    {
        return array_merge(
            DateTimeRules::required('state.dateFrom', 'state.timeFrom'),
            DateTimeRules::required('state.dateTo', 'state.timeTo'),
        );
    }

    /**
     * Because Livewire works with checkboxes the way it does, we need to prepare the array of checkboxes so it's properly handled by Livewire.
     *
     * @param array $items
     *
     * @return array
     */
    private function formatArrayForCheckbox(array $items) : array
    {
        return collect($items)->mapWithKeys(fn (string $key) : array => [
            $key => true,
        ])->toArray();
    }
}
