<?php

declare(strict_types=1);

use App\Services\Client\Client;
use App\Services\Client\Resources\Watcher;
use function Spatie\Snapshots\assertMatchesSnapshot;
use function Tests\createServerWithFixture;

it('should call [watcher.getEvents] and pass', function (): void {
    $server = createServerWithFixture('watcher/getEvents');

    $result = (new Watcher(Client::fromServer($server)))->getEvents();

    assertMatchesSnapshot($result);
});
