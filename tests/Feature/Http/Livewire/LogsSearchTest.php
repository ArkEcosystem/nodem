<?php

declare(strict_types=1);

use App\Http\Livewire\LogsSearch;
use Livewire\Livewire;

it('emits an event when term changes', function () : void {
    Livewire::test(LogsSearch::class, [
        'process' => 'relay',
    ])->set('state.term', 'term')->assertEmitted('changeSearchTerm', [
        'term'    => 'term',
        'process' => 'relay',
    ]);
});

it('emits an event when term is empty', function () : void {
    Livewire::test(LogsSearch::class, [
        'process' => 'relay',
    ])->set('state.term', '')->assertEmitted('changeSearchTerm', [
        'term'    => '',
        'process' => 'relay',
    ]);
});

it('can call a method to run the search', function () : void {
    Livewire::test(LogsSearch::class, [
        'process' => 'relay',
        'state'   => [
            'term' => 'hello',
        ],
    ])->call('performSearch')->assertEmitted('changeSearchTerm', [
        'term'    => 'hello',
        'process' => 'relay',
    ]);
});
