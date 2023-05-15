<?php

declare(strict_types=1);

use App\Enums\ProcessStatusEnum;
use App\Http\Livewire\ServerListItem;
use App\Models\Process;
use App\Models\Server;
use App\Models\User;
use Livewire\Livewire;

it('should not poll the server if there are no processes in a pending state', function ($status): void {
    $user = User::factory()->create(['email' => 'hello@basecode.sh']);

    $server = Server::factory()->create(['user_id' => $user->id]);

    Process::factory()->create(['server_id' => $server->id, 'type' => 'forger', 'name' => 'ark-core', 'status' => $status]);

    Livewire::actingAs($user)
        ->test(ServerListItem::class, ['model' => $server, 'showAs' => 'grid-item'])
        ->assertDontSeeHtml('wire:poll');
})->with([
    ProcessStatusEnum::ONLINE,
    ProcessStatusEnum::STOPPED,
    ProcessStatusEnum::ERRORED,
    ProcessStatusEnum::ONE_LAUNCH_STATUS,
    ProcessStatusEnum::UNDEFINED,
]);

it('should poll the server if is on a pending state', function ($status): void {
    $user = User::factory()->create(['email' => 'hello@basecode.sh']);

    $server = Server::factory()->create(['user_id' => $user->id]);

    Process::factory()->create(['server_id' => $server->id, 'type' => 'forger', 'name' => 'ark-forger', 'status' => $status]);
    Process::factory()->create(['server_id' => $server->id, 'type' => 'relay', 'name' => 'ark-relay', 'status' => ProcessStatusEnum::ONLINE]);

    Livewire::actingAs($user)
        ->test(ServerListItem::class, ['model' => $server, 'showAs' => 'grid-item'])
        ->assertSeeHtml('wire:poll');
})->with([
    ProcessStatusEnum::LAUNCHING,
    ProcessStatusEnum::STOPPING,
    ProcessStatusEnum::WAITING_RESTART,
]);
