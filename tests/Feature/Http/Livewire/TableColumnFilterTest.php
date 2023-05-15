<?php

declare(strict_types=1);

use App\Http\Livewire\TableColumnFilter;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

it('should show the filter', function (): void {
    $component = Livewire::test(TableColumnFilter::class, [
        'columns' => ['name'],
    ]);

    $component->assertSee('Name');
});

it('should emit event to update columns', function (): void {
    $component = Livewire::test(TableColumnFilter::class, [
        'columns' => ['name', 'core_ver', 'provider'],
    ]);

    $component
        ->call('toggleColumn', 'name')
        ->assertEmitted('columnRefresh', ['name' => true])
        ->call('toggleColumn', 'provider')
        ->assertEmitted('columnRefresh', ['name' => true, 'provider' => true])
        ->call('toggleColumn', 'provider')
        ->assertEmitted('columnRefresh', ['name' => true]);
});

it('should determine if a column is visible or not', function (): void {
    $user = User::factory()->create();

    $cacheKey = sprintf('hiddenColumns-%s', $user->id);

    Cache::shouldReceive('get')
        ->with($cacheKey, [])
        ->andReturn(['name' => true]);

    $component = Livewire::actingAs($user)->test(TableColumnFilter::class, [
        'columns' => ['name', 'provider'],
    ]);

    expect($component->instance()->isColumnVisible('name'))->toBeFalse();
    expect($component->instance()->isColumnVisible('provider'))->toBeTrue();
});

it('should not apply the filters globally to users', function () {
    $user       = User::factory()->create();
    $secondUser = User::factory()->create();

    $cacheKey           = sprintf('hiddenColumns-%s', $user->id);
    $secondUserCacheKey = sprintf('hiddenColumns-%s', $secondUser->id);

    Cache::shouldReceive('get')
        ->with($cacheKey, [])
        ->andReturn(['name' => true]);

    Cache::shouldReceive('get')
        ->with($secondUserCacheKey, [])
        ->andReturn([]);

    $component = Livewire::actingAs($user)->test(TableColumnFilter::class, [
        'columns' => ['name', 'provider'],
    ]);

    expect($component->instance()->isColumnVisible('name'))->toBeFalse();
    expect($component->instance()->isColumnVisible('provider'))->toBeTrue();

    $component = Livewire::actingAs($secondUser)->test(TableColumnFilter::class, [
        'columns' => ['name', 'provider'],
    ]);

    expect($component->instance()->isColumnVisible('name'))->toBeTrue();
    expect($component->instance()->isColumnVisible('provider'))->toBeTrue();
});
