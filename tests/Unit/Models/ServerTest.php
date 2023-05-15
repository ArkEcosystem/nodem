<?php

declare(strict_types=1);

use App\Enums\ServerUpdatingTasksEnum;
use App\Models\Server;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

it('should belong to a user', function (): void {
    $server = Server::factory()->create();

    expect($server->user())->toBeInstanceOf(BelongsTo::class);
});

it('should have many processes', function (): void {
    $server = Server::factory()->create();

    expect($server->processes())->toBeInstanceOf(HasMany::class);
});

it('should determine if the server uses basic auth', function (): void {
    $server = Server::factory()->create([
        'auth_username' => null,
        'auth_password' => null,
    ]);

    expect($server->usesBasicAuth())->toBeFalse();

    $server->update([
        'auth_username' => 'username',
    ]);

    expect($server->usesBasicAuth())->toBeFalse();

    $server->update([
        'auth_password' => 'password',
    ]);

    expect($server->usesBasicAuth())->toBeTrue();
});

it('should determine if the server uses an access key', function (): void {
    $server = Server::factory()->create([
        'auth_access_key' => null,
    ]);

    expect($server->usesAccessKey())->toBeFalse();

    $server->update([
        'auth_access_key' => 'access_key',
    ]);

    expect($server->usesAccessKey())->toBeTrue();
});

it('has a route', function () {
    $server = Server::factory()->create();

    expect($server->route())->toBe(route('server', $server->id));
});

it('has a logo', function () {
    $server = Server::factory()->create();

    expect($server->logo())->toBeNull();
});

it('has a fallback notification identifier', function () {
    $server = Server::factory()->create();

    expect($server->fallbackIdentifier())->toBe((string) $server->toViewModel()->id());
});

it('should determine if server is offline', function (): void {
    $server = Server::factory()->create([
        sprintf('extra_attributes->failed->%s', ServerUpdatingTasksEnum::UPDATING_SERVER_PING) => true,
    ]);

    expect($server->isOffline())->toBeTrue();
});

it('should determine if server is loading', function (): void {
    $server = Server::factory()->create([
        'extra_attributes->loading' => true,
    ]);

    expect($server->isLoading())->toBeTrue();
});

it('should determine if server is not loading', function (): void {
    $server = Server::factory()->create();

    expect($server->isLoading())->toBeFalse();
});

it('should determine if server process manager is running', function (): void {
    $server = Server::factory()->create([
        sprintf('extra_attributes->succeed->%s', ServerUpdatingTasksEnum::UPDATING_SERVER_PING) => true,
        sprintf('extra_attributes->succeed->%s', ServerUpdatingTasksEnum::SERVER_CORE_MANAGER_RUNNING) => true,
    ]);

    expect($server->isManagerRunning())->toBeTrue();
});

it('should determine if server process manager is not running', function (): void {
    $server = Server::factory()->create([
        sprintf('extra_attributes->succeed->%s', ServerUpdatingTasksEnum::UPDATING_SERVER_PING) => true,
        sprintf('extra_attributes->succeed->%s', ServerUpdatingTasksEnum::SERVER_CORE_MANAGER_RUNNING) => false,
    ]);

    expect($server->isManagerRunning())->toBeFalse();
    expect($server->isManagerNotRunning())->toBeTrue();

    $server = Server::factory()->create([
        sprintf('extra_attributes->succeed->%s', ServerUpdatingTasksEnum::UPDATING_SERVER_PING) => false,
        sprintf('extra_attributes->succeed->%s', ServerUpdatingTasksEnum::SERVER_CORE_MANAGER_RUNNING) => true,
    ]);

    expect($server->isManagerRunning())->toBeFalse();
    expect($server->isManagerNotRunning())->toBeTrue();
});

it('it flushes cached filters when deleted', function () {
    $server = Server::factory()->create();

    Cache::tags('server-filters:'.$server->id)->put('some-key', 'some-value');

    expect(Cache::tags('server-filters:'.$server->id)->has('some-key'))->toBeTrue();

    $server->delete();

    expect(Cache::tags('server-filters:'.$server->id)->has('some-key'))->toBeFalse();
});

it('adds the `loading` attribute for a task', function () {
    $server = Server::factory()->create();

    $server->markTaskAsStarted('task');

    expect($server->extra_attributes->get('loading.task'))->toBeTrue();
});

it('removes the `loading` attribute for a task', function () {
    $server = Server::factory()->create();

    $server->markTaskAsStarted('task');
    $server->markTaskAsStarted('task2');

    expect($server->extra_attributes->get('loading.task'))->toBeTrue();
    expect($server->extra_attributes->get('loading.task2'))->toBeTrue();

    $server->markTaskAsFinished('task');

    expect($server->extra_attributes->get('loading.task'))->toBeNull();
    expect($server->extra_attributes->get('loading.task2'))->toBeTrue();
});

it('completely removes the `loading` attribute if not more loading tasks', function () {
    $server = Server::factory()->create();

    $server->markTaskAsStarted('task');

    expect($server->extra_attributes->get('loading.task'))->toBeTrue();

    $server->markTaskAsFinished('task');

    expect($server->extra_attributes->get('loading'))->toBeNull();
});

it('adds the succeed attribute when a job succeed', function () {
    $server = Server::factory()->create();

    $server->markTaskAsSucceed('task');

    expect($server->extra_attributes->get('succeed.task'))->toBeTrue();
});

it('adds the failed attribute when a job fails', function () {
    $server = Server::factory()->create();

    $server->markTaskAsFailed('task');

    expect($server->extra_attributes->get('failed.task'))->toBeTrue();
});

it('removes the failed attribute when a job succeed', function () {
    $server = Server::factory()->create();

    $server->markTaskAsFailed('task');

    expect($server->extra_attributes->get('failed.task'))->toBeTrue();

    $server->markTaskAsSucceed('task');

    expect($server->extra_attributes->get('failed.task'))->toBeNull();
    expect($server->extra_attributes->get('succeed.task'))->toBeTrue();
});

it('removes the succeed attribute when a job fails', function () {
    $server = Server::factory()->create();

    $server->markTaskAsSucceed('task');

    expect($server->extra_attributes->get('succeed.task'))->toBeTrue();

    $server->markTaskAsFailed('task');

    expect($server->extra_attributes->get('failed.task'))->toBeTrue();
    expect($server->extra_attributes->get('succeed.task'))->toBeNull();
});

it('completely removes the succeed attribute no succeed flags left', function () {
    // By default the server have `succeed->updating_server_ping` and `succeed->updating_server_core`
    $server = Server::factory()->create();

    expect($server->extra_attributes->get('succeed'))->not->toBeNull();

    $server->markTaskAsFailed(ServerUpdatingTasksEnum::UPDATING_SERVER_PING);

    expect($server->extra_attributes->get('succeed'))->not->toBeNull();

    $server->markTaskAsFailed(ServerUpdatingTasksEnum::SERVER_CORE_MANAGER_RUNNING);

    expect($server->extra_attributes->get('succeed'))->toBeNull();
});
