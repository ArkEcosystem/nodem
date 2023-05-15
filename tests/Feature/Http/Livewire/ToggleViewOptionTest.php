<?php

declare(strict_types=1);

use App\Http\Livewire\ToggleViewOption;
use App\Models\User;
use Livewire\Livewire;

it('stores in the database for the user when toggled', function () : void {
    $user = User::factory()->create();

    expect($user->defaultTableView())->toBe('list');

    Livewire::actingAs($user)->test(ToggleViewOption::class, [
        'tableView' => $user->defaultTableView(),
        'disabled'  => false,
    ])->set('tableView', 'grid')->assertSet('tableView', 'grid');

    expect($user->fresh()->defaultTableView())->toBe('grid');
});

it('only allows valid values', function () : void {
    $user = User::factory()->create();

    $user->setDefaultTableView('grid');
    expect($user->defaultTableView())->toBe('grid');

    Livewire::actingAs($user)->test(ToggleViewOption::class, [
        'tableView' => $user->defaultTableView(),
        'disabled'  => false,
    ])->set('tableView', 'something-unknown')->assertSet('tableView', 'list');

    expect($user->fresh()->defaultTableView())->toBe('list');
});

it('recovers if user is not logged in', function () : void {
    Livewire::test(ToggleViewOption::class, [
        'tableView' => 'grid',
        'disabled'  => false,
    ])->set('tableView', 'something-unknown')->assertSet('tableView', 'list');
});

it('renders a component', function () : void {
    Livewire::test(ToggleViewOption::class, [
        'tableView' => 'grid',
        'disabled'  => false,
    ])->assertViewHas('tableView', 'grid')->assertViewHas('disabled', false);
});
