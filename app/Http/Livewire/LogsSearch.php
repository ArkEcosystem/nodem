<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use Livewire\Component;

final class LogsSearch extends Component
{
    public string $process;

    public array $state = [
        'term' => '',
    ];

    public function updatedState() : void
    {
        $this->performSearch();
    }

    public function performSearch(): void
    {
        $this->emit('changeSearchTerm', [
            'term'    => $this->state['term'],
            'process' => $this->process,
        ]);
    }
}
