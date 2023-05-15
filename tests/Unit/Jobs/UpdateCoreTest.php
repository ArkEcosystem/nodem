<?php

declare(strict_types=1);

use App\Enums\ServerUpdatingTasksEnum;
use App\Jobs\UpdateCore;
use App\Jobs\UpdateServerCoreVersion;
use App\Models\Process;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use function Tests\createServerWithFixture;

it('should send a request to update the core of the server to the latest version', function (): void {
    Bus::fake();

    $server = createServerWithFixture('info/coreVersion');

    Process::factory()->online()->create([
        'server_id' => $server->id,
        'name'      => 'core',
        'type'      => 'relay',
    ]);

    Process::factory()->stopped()->create([
        'server_id' => $server->id,
        'name'      => 'core',
        'type'      => 'forger',
    ]);

    (new UpdateCore($server, $server->user))->handle();

    Bus::assertDispatched(UpdateServerCoreVersion::class, fn ($queue) => expect($queue->middleware()[0])
            ->toBeInstanceOf(WithoutOverlapping::class)
            ->toHaveKey('key', $server->getKey()));

    Http::assertSent(function ($request) use ($server): bool {
        return $request->url() === $server->host &&
            $request['method'] === 'info.coreUpdate' &&
            $request['params']->restart === true;
    });
});

it('should add a loading state', function () {
    $server = createServerWithFixture('info/coreVersion');

    new UpdateCore($server, $server->user);

    $server->refresh();

    expect($server->getMetaAttribute('loading.'.ServerUpdatingTasksEnum::UPDATING_SERVER_CORE))->toBeTrue();
    expect($server->isLoading())->toBeTrue();
});
