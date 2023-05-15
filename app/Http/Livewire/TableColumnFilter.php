<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use ARKEcosystem\Foundation\Fortify\Components\Concerns\InteractsWithUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Livewire\Component;

final class TableColumnFilter extends Component
{
    use InteractsWithUser;

    public array $columns = [];

    /** @var mixed */
    protected $listeners = [
        'serverAdded' => '$refresh',
    ];

    public function mount(array $columns): void
    {
        $this->columns = array_map(fn ($column) => true, array_flip($columns));
    }

    public function render(): View
    {
        return view('livewire.table-column-filter', [
            'disabled' => $this->user?->servers()->count() === 0,
        ]);
    }

    public function toggleColumn(string $column): void
    {
        $cache = Cache::get($this->getCacheKey(), []);

        if (array_key_exists($column, $cache)) {
            unset($cache[$column]);
        } else {
            $cache[$column] = true;
        }

        Cache::put($this->getCacheKey(), $cache);

        $this->emit('columnRefresh', $cache);
    }

    public function isColumnVisible(string $column): bool
    {
        $cache = Cache::get($this->getCacheKey(), []);

        return ! array_key_exists($column, $cache);
    }

    private function getCacheKey(): string
    {
        return sprintf('hiddenColumns-%s', Auth::id());
    }
}
