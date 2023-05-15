<?php

declare(strict_types=1);

use App\Models\DatabaseNotification;
use App\Models\Server;

it('should have a name', function (): void {
    $notification = DatabaseNotification::factory()->ownedBy(Server::factory()->create([
        'name' => 'Server #1',
    ]))->create();

    expect($notification->name())->toBe('Server #1');
});

it('should have a logo', function (): void {
    $notification = DatabaseNotification::factory()->ownedBy(Server::factory()->create())->create();

    expect($notification->logo())->toBe(url('/images/logo.svg'));
});

it('should have a route', function (): void {
    $server = Server::factory()->create();

    $notification = DatabaseNotification::factory()->ownedBy($server)->create();

    expect($notification->route())->toBe(route('server', $server->id));
});
