<?php

declare(strict_types=1);

use App\Services\Client\Client;
use App\Services\Client\Resources\Configuration;
use function Spatie\Snapshots\assertMatchesSnapshot;
use function Tests\createServerWithFixture;

it('should call [configuration.getEnv] and pass', function (): void {
    $server = createServerWithFixture('configuration/getEnv');

    $result = (new Configuration(Client::fromServer($server)))->getEnv();

    assertMatchesSnapshot($result);
});

it('should call [configuration.setEnv] and pass', function (): void {
    $server = createServerWithFixture('configuration/setEnv');

    $result = (new Configuration(Client::fromServer($server)))->setEnv([]);

    assertMatchesSnapshot($result);
});

it('should call [configuration.getPlugins] and pass', function (): void {
    $server = createServerWithFixture('configuration/getPlugins');

    $result = (new Configuration(Client::fromServer($server)))->getPlugins();

    assertMatchesSnapshot($result);
});

it('should call [configuration.setPlugins] and pass', function (): void {
    $server = createServerWithFixture('configuration/setPlugins');

    $result = (new Configuration(Client::fromServer($server)))->setPlugins([]);

    assertMatchesSnapshot($result);
});
