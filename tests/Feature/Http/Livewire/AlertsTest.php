<?php

declare(strict_types=1);

use App\Cache\AlertStore;
use App\DTO\Alert;
use App\Enums\AlertType;
use App\Http\Livewire\Alerts;
use App\Models\User;
use Livewire\Livewire;

it('only fetches the alerts that correspond to the user', function () {
    $user  = User::factory()->create();
    $user2 = User::factory()->create();

    AlertStore::push($user, new Alert(
        'alert1',
        'warning',
        'Server 1',
    ));

    AlertStore::push($user2, new Alert(
        'alert2',
        'warning',
        'Server 1',
    ));

    AlertStore::push($user, new Alert(
        'alert1-a',
        'warning',
        'Server 1',
    ));

    expect(count(AlertStore::getAll($user)))->toBe(2);

    expect(count(AlertStore::getAll($user2)))->toBe(1);
});

it('emits one alert for 3 failed tasks of one server', function () {
    $user = User::factory()->create();

    $alerts = collect([
        new Alert(
            AlertType::RESTART_SERVER,
            'warning',
            'Server 1',
        ),
        new Alert(
            AlertType::START_SERVER,
            'warning',
            'Server 1',
        ),
        new Alert(
            AlertType::UPDATING_PROCESSES,
            'warning',
            'Server 1',
        ),
    ]);

    $alerts->each(function (Alert $alert) use ($user) {
        AlertStore::push($user, $alert);
    });

    $component = Livewire::actingAs($user)
        ->test(Alerts::class)
        ->assertNotEmitted('toastMessage');

    expect(count(AlertStore::getAll($user)))->toBe(3);

    $component->call('fetch');

    $component->assertEmitted('toastMessage', [
        '<strong>Server 1</strong>'.
        "<ul class='ml-3 list-disc'>".
        '<li>'.trans('alerts.'.AlertType::RESTART_SERVER).'</li>'.
        '<li>'.trans('alerts.'.AlertType::START_SERVER).'</li>'.
        '<li>'.trans('alerts.'.AlertType::UPDATING_PROCESSES).'</li>'.
        '</ul>',
        'warning',
    ]);

    expect(count(AlertStore::getAll($user)))->toBe(0);
});

it('emits separate alerts for each server', function () {
    $user = User::factory()->create();

    $alerts = collect([
        new Alert(
            AlertType::RESTART_SERVER,
            'warning',
            'Server 1',
        ),
        new Alert(
            AlertType::START_SERVER,
            'warning',
            'Server 1',
        ),
        new Alert(
            AlertType::UPDATING_PROCESSES,
            'warning',
            'Server 2',
        ),
    ]);

    $alerts->each(function (Alert $alert) use ($user) {
        AlertStore::push($user, $alert);
    });

    $component = Livewire::actingAs($user)
        ->test(Alerts::class)
        ->assertNotEmitted('toastMessage');

    expect(count(AlertStore::getAll($user)))->toBe(3);

    $component->call('fetch');

    $component->assertEmitted('toastMessage', [
        '<strong>Server 1</strong>'.
        "<ul class='ml-3 list-disc'>".
        '<li>'.trans('alerts.'.AlertType::RESTART_SERVER).'</li>'.
        '<li>'.trans('alerts.'.AlertType::START_SERVER).'</li>'.
        '</ul>',
        'warning',
    ]);

    $component->assertEmitted('toastMessage', [
        '<strong>Server 2</strong>'.
        "<ul class='ml-3 list-disc'>".
        '<li>'.trans('alerts.'.AlertType::UPDATING_PROCESSES).'</li>'.
        '</ul>',
        'warning',
    ]);

    expect(count(AlertStore::getAll($user)))->toBe(0);
});

it('emits separate alerts for each alert type', function () {
    $user = User::factory()->create();

    $alerts = collect([
        new Alert(
            AlertType::RESTART_SERVER,
            'warning',
            'Server 1',
        ),
        new Alert(
            AlertType::START_SERVER,
            'warning',
            'Server 1',
        ),
        new Alert(
            AlertType::UPDATING_PROCESSES,
            'success',
            'Server 1',
        ),
    ]);

    $alerts->each(function (Alert $alert) use ($user) {
        AlertStore::push($user, $alert);
    });

    $component = Livewire::actingAs($user)
        ->test(Alerts::class)
        ->assertNotEmitted('toastMessage');

    expect(count(AlertStore::getAll($user)))->toBe(3);

    $component->call('fetch');

    $component->assertEmitted('toastMessage', [
        '<strong>Server 1</strong>'.
        "<ul class='ml-3 list-disc'>".
        '<li>'.trans('alerts.'.AlertType::RESTART_SERVER).'</li>'.
        '<li>'.trans('alerts.'.AlertType::START_SERVER).'</li>'.
        '</ul>',
        'warning',
    ]);

    $component->assertEmitted('toastMessage', [
        '<strong>Server 1</strong>'.
        "<ul class='ml-3 list-disc'>".
        '<li>'.trans('alerts.'.AlertType::UPDATING_PROCESSES).'</li>'.
        '</ul>',
        'success',
    ]);

    expect(count(AlertStore::getAll($user)))->toBe(0);
});

it('should remove duplicate messages', function () {
    $user = User::factory()->create();

    $alerts = collect([
        new Alert(
            AlertType::RESTART_SERVER,
            'warning',
            'Server 1',
        ),
        new Alert(
            AlertType::RESTART_SERVER,
            'warning',
            'Server 1',
        ),
        new Alert(
            AlertType::RESTART_SERVER,
            'warning',
            'Server 1',
        ),
        new Alert(
            AlertType::UPDATING_PROCESSES,
            'warning',
            'Server 1',
        ),
    ]);

    $alerts->each(function (Alert $alert) use ($user) {
        AlertStore::push($user, $alert);
    });

    $component = Livewire::actingAs($user)
        ->test(Alerts::class)
        ->call('fetch');

    $component->assertEmitted('toastMessage', [
        '<strong>Server 1</strong>'.
        "<ul class='ml-3 list-disc'>".
        '<li>'.trans('alerts.'.AlertType::RESTART_SERVER).'</li>'.
        '<li>'.trans('alerts.'.AlertType::UPDATING_PROCESSES).'</li>'.
        '</ul>',
        'warning',
    ]);

    expect(count(AlertStore::getAll($user)))->toBe(0);
});
