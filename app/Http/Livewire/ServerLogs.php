<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Actions\SearchLogs;
use App\Http\Livewire\Concerns\InteractsWithLogFilters;
use App\Models\Server;
use App\ViewModels\ProcessLogViewModel;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Throwable;

/**
 * @property \App\Models\User $user
 */
final class ServerLogs extends Component
{
    use InteractsWithLogFilters;

    public const LIMIT = 100;

    public Server $server;

    /**
     * @var array<string, array>
     */
    public array $processes = [];

    /** @var mixed */
    protected $listeners = [
        'changeSearchTerm', 'applyFilters',
    ];

    public function mount() : void
    {
        $server = $this->server->toViewModel();

        if ($server->prefersCombined()) {
            $this->initializeProcess('core');

            $this->processes['core']['filters'] = array_merge($this->processes['core']['filters'], $this->cachedFilters('core'));
        } else {
            if ($server->hasRelay()) {
                $this->initializeProcess('relay');

                $this->processes['relay']['filters'] = array_merge($this->processes['relay']['filters'], $this->cachedFilters('relay'));
            }

            if ($server->hasForger()) {
                $this->initializeProcess('forger');

                $this->processes['forger']['filters'] = array_merge($this->processes['forger']['filters'], $this->cachedFilters('forger'));
            }
        }
    }

    public function changeSearchTerm(array $payload) : void
    {
        $process = (string) $payload['process'];

        if (! $this->isValidProcess($process)) {
            return;
        }

        $this->processes[$process]['search'] = (string) $payload['term'];
        $this->processes[$process]['logs']   = $this->findLogs($process);
    }

    public function applyFilters(array $payload) : void
    {
        $process = (string) $payload['process'];

        $identifier = $payload['identifier'];

        if (! $this->isValidProcess($process)) {
            return;
        }

        $this->processes[$process]['filters'] = [
            'from'   => $payload['startDate'] ?? null,
            'to'     => $payload['endDate'] ?? null,
            'levels' => $payload['levels'] ?? [],
        ];

        $this->cacheFilters($this->processes[$process]['filters'], $process);

        $this->processes[$process]['logs'] = $this->findLogs($process);

        $this->emit('filtersApplied:'.$identifier);
    }

    public function loadLogs() : void
    {
        foreach (array_keys($this->processes) as $process) {
            $this->processes[$process]['logs'] = $this->findLogs($process);
        }
    }

    public function getLogInstancesProperty() : array
    {
        return collect($this->processes)->mapWithKeys(fn (array $data, string $process) : array => [
            $process => array_map(fn ($p) => new ProcessLogViewModel($p), $data['logs'] ?? []),
        ])->all();
    }

    public function render() : View
    {
        return view('livewire.server-logs', [
            'viewModel' => $this->server->toViewModel(),
        ]);
    }

    private function findLogs(string $process) : array
    {
        $query = SearchLogs::new($this->server)
                            ->limit(static::LIMIT)
                            ->process($process);

        $state = $this->processes[$process];

        // Apply search query...
        if ($state['search'] !== null && $state['search'] !== '') {
            $query->term($state['search']);
        }

        // Apply 'to' date filters...
        if ($state['filters']['from'] !== null) {
            $query->timeFrom(Carbon::createFromTimestamp($state['filters']['from']));
        }

        // Apply 'to' date filters...
        if ($state['filters']['to'] !== null) {
            $query->timeTo(Carbon::createFromTimestamp($state['filters']['to']));
        }

        // Apply levels filter...
        if (count($state['filters']['levels']) > 0) {
            $query->levels(collect($state['filters']['levels'])->filter()->keys()->all());
        }

        return $this->withErrorRecovery(
            $process,
            fn () => collect($query->search()['data'])->sortByDesc('id')->toArray()
        );
    }

    private function initializeProcess(string $process) : void
    {
        $this->processes[$process] = [
            'loaded'  => false,
            'failed'  => false,
            'logs'    => [],
            'search'  => '',
            'filters' => [
                'from'   => null,
                'to'     => null,
                'levels' => [],
            ],
        ];
    }

    private function isValidProcess(string $process) : bool
    {
        return array_key_exists($process, $this->processes);
    }

    private function withErrorRecovery(string $process, Closure $callback) : array
    {
        try {
            $response = $callback();

            $this->processes[$process]['loaded']  = true;
            $this->processes[$process]['failed']  = false;

            return $response;
        } catch (Throwable $e) {
            report($e);

            $this->processes[$process]['failed'] = true;

            return [];
        }
    }
}
