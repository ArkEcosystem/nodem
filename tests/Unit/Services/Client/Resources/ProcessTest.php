<?php

declare(strict_types=1);

use App\Services\Client\Client;
use App\Services\Client\Resources\Process;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use function Spatie\Snapshots\assertMatchesSnapshot;
use function Tests\createServerWithFixture;

it('should call [process.list] and pass', function (): void {
    $server = createServerWithFixture('process/list');

    $result = (new Process(Client::fromServer($server)))->list();

    assertMatchesSnapshot($result);
});

it('should call [process.stop] and pass', function (): void {
    $server = createServerWithFixture('process/stop');

    $result = (new Process(Client::fromServer($server)))->stop('core');

    assertMatchesSnapshot($result);
});

it('should call [process.start] and pass', function (): void {
    $server = createServerWithFixture('process/start');

    $result = (new Process(Client::fromServer($server)))->start('core');

    Http::assertSent(function (Request $request) {
        return collect($request->data()['params'])->keys()->contains('args');
    });

    assertMatchesSnapshot($result);
});

it('should call [process.restart] and pass', function (): void {
    $server = createServerWithFixture('process/restart');

    $result = (new Process(Client::fromServer($server)))->restart('core');

    assertMatchesSnapshot($result);
});

it('should call [process.delete] and pass', function (): void {
    $server = createServerWithFixture('process/delete');

    $result = (new Process(Client::fromServer($server)))->delete('core');

    assertMatchesSnapshot($result);
});
