<?php

declare(strict_types=1);

use App\Enums\TeamMemberPermission;
use App\Enums\TeamMemberRole;
use App\Http\Livewire\DeleteServerModal;
use App\Models\Server;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Livewire\Livewire;

it('should show the modal', function (): void {
    $user = User::factory()->create();

    $server = Server::factory()->create(['user_id' => $user->id]);

    $component = Livewire::actingAs($user)->test(DeleteServerModal::class);
    $component->call('open', $server->id);
    $component->assertSet('modalShown', true);
    $component->assertSet('server', fn ($property) => $property->is($server));
});

it('should hide the modal', function (): void {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)->test(DeleteServerModal::class);
    $component->call('close');
    $component->assertSet('modalShown', false);
    $component->assertSet('server', null);
    $component->assertSet('serverNameConfirmation', null);
});

it('should reset any validation error when closing the modal', function (): void {
    $user = User::factory()->create();

    $server = Server::factory()->create(['name' => 'foo', 'user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(DeleteServerModal::class)
        ->call('open', $server->id)
        ->set('serverNameConfirmation', 'bar')
        ->assertHasErrors('serverNameConfirmation')
        ->call('close')
        ->assertSet('modalShown', false)
        ->assertSet('server', null)
        ->assertSet('serverNameConfirmation', null)
        ->assertHasNoErrors();
});

it('should not be possible to delete a server without permission', function () {
    $this->seed(AccessControlSeeder::class);

    $owner = User::factory()->create();
    $user  = User::factory()->create();
    $user->joinAs(TeamMemberRole::MAINTAINER, $owner);

    expect($user->can(TeamMemberPermission::SERVER_DELETE))->toBeFalse();

    Livewire::actingAs($user)
        ->test(DeleteServerModal::class)
        ->call('deleteServer')
        ->assertForbidden();
});

it('shows a validation error if server name is not the same as the one user wants to delete', function (): void {
    $user = User::factory()->create();

    $server = Server::factory()->create(['name' => 'foo', 'user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(DeleteServerModal::class)
        ->call('open', $server->id)
        ->set('serverNameConfirmation', 'bar')
        ->assertHasErrors('serverNameConfirmation');
});

it('should not be able to submit if there is any validation error', function (): void {
    $user = User::factory()->create();

    $server = Server::factory()->create(['name' => 'foo', 'user_id' => $user->id]);

    $component = Livewire::actingAs($user)
        ->test(DeleteServerModal::class)
        ->call('open', $server->id)
        ->set('serverNameConfirmation', 'bar')
        ->assertHasErrors('serverNameConfirmation');

    expect($component->instance()->getCanSubmitProperty())->toBeFalse();
});

it('should be able to delete a server', function (): void {
    $user = User::factory()->create();

    $server = Server::factory()->create(['name' => 'foo', 'user_id' => $user->id]);

    $component = Livewire::actingAs($user)
        ->test(DeleteServerModal::class)
        ->call('open', $server->id)
        ->set('serverNameConfirmation', 'foo')
        ->assertHasNoErrors();

    expect($component->instance()->getCanSubmitProperty())->toBeTrue();
    expect(Server::count())->toBe(1);

    $component
        ->call('deleteServer')
        ->assertSet('modalShown', false)
        ->assertSet('server', null)
        ->assertSet('serverNameConfirmation', null)
        ->assertRedirect(route('home'));

    expect(Server::count())->toBe(0);
});
