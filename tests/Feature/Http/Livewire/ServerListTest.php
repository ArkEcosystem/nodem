<?php

declare(strict_types=1);

use App\Http\Livewire\ServerList;
use App\Models\Process;
use App\Models\Server;
use App\Models\User;
use App\ViewModels\ServerViewModel;
use Carbon\Carbon;
use Livewire\Livewire;

it('should list the servers of the authenticated user', function (): void {
    $user = User::factory()->create(['email' => 'hello@basecode.sh']);

    $servers = Server::factory(50)
        ->sequence(fn ($sequence) => ['created_at' => Carbon::now()->subMinutes($sequence->index)])
        ->create(['user_id' => $user->id])
        ->sortByDesc('id');

    $servers->each(function ($server): void {
        Process::factory()->create(['server_id' => $server->id, 'type' => 'core', 'name' => 'ark-core']);
        Process::factory()->create(['server_id' => $server->id, 'type' => 'forger', 'name' => 'ark-forger']);
        Process::factory()->create(['server_id' => $server->id, 'type' => 'relay', 'name' => 'ark-relay']);
    });

    $component = Livewire::actingAs($user)->test(ServerList::class);

    foreach ($servers->take(10) as $server) {
        $component->assertSee((new ServerViewModel($server))->hostShort());
    }

    foreach ($servers->skip(10) as $server) {
        $component->assertDontSee((new ServerViewModel($server))->hostShort());
    }
});
