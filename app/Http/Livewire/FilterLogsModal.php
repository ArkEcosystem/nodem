<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Enums\LogLevel;
use App\Http\Livewire\Concerns\InteractsWithLogFilters;
use App\Models\Server;
use App\Rules\DateTimeRules;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasModal;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * @property \App\Models\User $user
 */
final class FilterLogsModal extends Component
{
    use HasModal;
    use InteractsWithLogFilters;

    public Server $server;

    public string $process;

    public bool $busy = false;

    public bool $closeAfterFilterApplied = false;

    public array $state = [
        'dateFrom' => null,
        'dateTo'   => null,
        'timeFrom' => null,
        'timeTo'   => null,
        'levels'   => [],
    ];

    public function getListeners(): array
    {
        return [
            'showFilterLogsModal:'.$this->process => 'showFilterLogsModal',
            'filtersApplied:'.$this->process      => 'filtersApplied',
        ];
    }

    public function mount(): void
    {
        $state = $this->cachedFilters();

        if (count($state) > 0) {
            $from = $state['from'] ? Carbon::createFromTimestamp($state['from']) : null;
            $to   = $state['to'] ? Carbon::createFromTimestamp($state['to']) : null;

            $this->state = [
                'dateFrom'  => optional($from)->format('d.m.Y'),
                'timeFrom'  => optional($from)->format('H:i:s'),
                'dateTo'    => optional($to)->format('d.m.Y'),
                'timeTo'    => optional($to)->format('H:i:s'),
                'levels'    => $state['levels'],
            ];
        }
    }

    public function updatedState() : void
    {
        $this->state['timeFrom'] = $this->formatTimeInputPayload($this->state['timeFrom'] ?? '');
        $this->state['timeTo']   = $this->formatTimeInputPayload($this->state['timeTo'] ?? '');
    }

    public function showFilterLogsModal() : void
    {
        $this->openModal();
    }

    public function submit() : void
    {
        $this->performValidation();

        $this->emit('applyFilters', [
            'identifier' => $this->process,
            'process'    => $this->process,
            'startDate'  => optional($this->startDate())->unix(),
            'endDate'    => optional($this->endDate())->unix(),
            'levels'     => $this->state['levels'],
        ]);

        $this->busy = true;

        $this->closeAfterFilterApplied = true;
    }

    public function filtersApplied(): void
    {
        if ($this->busy === false) {
            return;
        }

        $this->busy = false;

        if ($this->closeAfterFilterApplied) {
            $this->closeAfterFilterApplied = false;
            $this->closeModal();
        }
    }

    public function resetFilters() : void
    {
        $this->resetErrorBag();

        $this->reset('state');

        $this->emit('applyFilters', [
            'identifier' => $this->process,
            'process'    => $this->process,
        ]);

        $this->busy = true;

        $this->closeAfterFilterApplied = true;
    }

    public function render() : View
    {
        return view('livewire.filter-logs-modal', [
            'levels' => LogLevel::withLabels(),
        ]);
    }

    private function performValidation() : void
    {
        $this->resetErrorBag();

        validator([
            'state' => $this->state,
        ], $this->validationRules(), $this->messages())->after(function (Validator $validator) : void {
            $this->validateDates($validator);
        })->validate();
    }

    private function validationRules() : array
    {
        return array_merge(
            DateTimeRules::nullable('state.dateFrom', 'state.timeFrom'),
            DateTimeRules::nullable('state.dateTo', 'state.timeTo'),
            [
                'state.levels' => 'array',
            ]
        );
    }
}
