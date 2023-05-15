<?php

declare(strict_types=1);

use App\Services\Client\Client;
use App\Services\Client\Resources\Plugin;
use function Spatie\Snapshots\assertMatchesSnapshot;
use function Tests\createServerWithFixture;

it('should call [plugin.update] and pass', function (): void {
    $server = createServerWithFixture('plugin/update');

    $result = (new Plugin(Client::fromServer($server)))->update();

    assertMatchesSnapshot($result);
});
