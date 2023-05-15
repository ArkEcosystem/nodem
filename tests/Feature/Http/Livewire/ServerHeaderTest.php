<?php

declare(strict_types=1);

use App\Http\Livewire\ServerHeader;
use App\Models\Server;
use App\Models\User;
use Livewire\Livewire;

it('should render', function (): void {
    $user   = User::factory()->create(['email' => 'hello@basecode.sh']);
    $server = Server::factory()->create();

    Livewire::actingAs($user)
        ->test(ServerHeader::class, ['model' => $server])
        ->assertSee('Provider');
});
