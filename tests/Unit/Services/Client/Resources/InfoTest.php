<?php

declare(strict_types=1);

use App\Services\Client\Client;
use App\Services\Client\Resources\Info;
use function Spatie\Snapshots\assertMatchesSnapshot;
use function Tests\createServerWithFixture;

it('should call [info.coreVersion] and pass', function (): void {
    $server = createServerWithFixture('info/coreVersion');

    $result = (new Info(Client::fromServer($server)))->coreVersion();

    assertMatchesSnapshot($result);
});

it('should call [info.coreStatus] and pass', function (): void {
    $server = createServerWithFixture('info/coreStatus');

    $result = (new Info(Client::fromServer($server)))->coreStatus();

    assertMatchesSnapshot($result);
});

it('should call [info.blockchainHeight] and pass', function (): void {
    $server = createServerWithFixture('info/blockchainHeight');

    $result = (new Info(Client::fromServer($server)))->blockchainHeight();

    assertMatchesSnapshot($result);
});

it('should call [info.resources] and pass', function (): void {
    $server = createServerWithFixture('info/resources');

    $result = (new Info(Client::fromServer($server)))->resources();

    assertMatchesSnapshot($result);
});

it('should call [info.databaseSize] and pass', function (): void {
    $server = createServerWithFixture('info/databaseSize');

    $result = (new Info(Client::fromServer($server)))->databaseSize();

    expect($result)->toBeArray();
});

it('should call [info.nextForgingSlot] and pass', function (): void {
    $server = createServerWithFixture('info/nextForgingSlot');

    $result = (new Info(Client::fromServer($server)))->nextForgingSlot();

    assertMatchesSnapshot($result);
});

it('should call [info.lastForgedBlock] and pass', function (): void {
    $server = createServerWithFixture('info/lastForgedBlock');

    $result = (new Info(Client::fromServer($server)))->lastForgedBlock();

    assertMatchesSnapshot($result);
});

it('should call [info.currentDelegate] and pass', function (): void {
    $server = createServerWithFixture('info/currentDelegate');

    $result = (new Info(Client::fromServer($server)))->currentDelegate();

    assertMatchesSnapshot($result);
});

it('should call [info.coreUpdate] and pass', function (): void {
    $server = createServerWithFixture('info/coreUpdate');

    $result = (new Info(Client::fromServer($server)))->coreUpdate();

    assertMatchesSnapshot($result);
});
