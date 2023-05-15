<?php

declare(strict_types=1);

use App\Models\Server;
use App\Services\Client\Resources\Configuration;
use App\Services\Client\Resources\Info;
use App\Services\Client\Resources\Log;
use App\Services\Client\Resources\Plugin;
use App\Services\Client\Resources\Process;
use App\Services\Client\Resources\Snapshot;
use App\Services\Client\Resources\Watcher;
use App\Services\Client\RPC;

it('should have access to the log methods', function (): void {
    $subject = RPC::fromServer(Server::factory()->create());

    expect($subject->log())->toBeInstanceOf(Log::class);
});

it('should have access to the configuration methods', function (): void {
    $subject = RPC::fromServer(Server::factory()->create());

    expect($subject->configuration())->toBeInstanceOf(Configuration::class);
});

it('should have access to the info methods', function (): void {
    $subject = RPC::fromServer(Server::factory()->create());

    expect($subject->info())->toBeInstanceOf(Info::class);
});

it('should have access to the process methods', function (): void {
    $subject = RPC::fromServer(Server::factory()->create());

    expect($subject->process())->toBeInstanceOf(Process::class);
});

it('should have access to the snapshot methods', function (): void {
    $subject = RPC::fromServer(Server::factory()->create());

    expect($subject->snapshot())->toBeInstanceOf(Snapshot::class);
});

it('should have access to the watcher methods', function (): void {
    $subject = RPC::fromServer(Server::factory()->create());

    expect($subject->watcher())->toBeInstanceOf(Watcher::class);
});

it('should have access to the plugin methods', function (): void {
    $subject = RPC::fromServer(Server::factory()->create());

    expect($subject->plugin())->toBeInstanceOf(Plugin::class);
});
