<?php

declare(strict_types=1);

use App\Services\Client\Client;
use App\Services\Client\Resources\Snapshot;
use function Spatie\Snapshots\assertMatchesSnapshot;
use function Tests\createServerWithFixture;

it('should call [snapshot.list] and pass', function (): void {
    $server = createServerWithFixture('snapshot/list');

    $result = (new Snapshot(Client::fromServer($server)))->list();

    assertMatchesSnapshot($result);
});

it('should call [snapshot.delete] and pass', function (): void {
    $server = createServerWithFixture('snapshot/delete');

    $result = (new Snapshot(Client::fromServer($server)))->delete();

    assertMatchesSnapshot($result);
});

it('should call [snapshot.create] and pass', function (): void {
    $server = createServerWithFixture('snapshot/create');

    $result = (new Snapshot(Client::fromServer($server)))->create();

    assertMatchesSnapshot($result);
});

it('should call [snapshot.restore] and pass', function (): void {
    $server = createServerWithFixture('snapshot/restore');

    $result = (new Snapshot(Client::fromServer($server)))->restore();

    assertMatchesSnapshot($result);
});
