<?php

declare(strict_types=1);

use App\Http\Livewire\Bip38PasswordModal;
use App\Models\Server;
use App\Models\User;
use Livewire\Livewire;

it('should show the modal when the `askForBip38Password` event is called', function (): void {
    $user = User::factory()->create();

    $server = Server::factory()->create(['user_id' => $user->id]);

    $component = Livewire::actingAs($user)->test(Bip38PasswordModal::class);

    $component->emit('askForBip38Password', [
        'start',
        'relay',
        $server->id,
    ]);

    $component->assertSet('modalShown', true);
    $component->assertSet('action', 'start');
    $component->assertSet('processType', 'relay');
    $component->assertSet('server', fn ($property) => $property->is($server));
});

it('should hide the modal and reset the data', function (): void {
    $server = Server::factory()->create();

    $component = Livewire::actingAs($server->user)
        ->test(Bip38PasswordModal::class)
        ->emit('askForBip38Password', [
            'start',
            'relay',
            $server->id,
        ]);

    $component->call('closeModal');
    $component->assertSet('modalShown', false);
    $component->assertSet('action', null);
    $component->assertSet('processType', null);
    $component->assertSet('server', null);
});

it('validates a password is set', function (): void {
    $server = Server::factory()->create();

    Livewire::actingAs($server->user)
        ->test(Bip38PasswordModal::class)
        ->emit('askForBip38Password', [
            'start',
            'relay',
            $server->id,
        ])
        ->set('bip38Password', '')
        ->call('submit')
        ->assertHasErrors('bip38Password');
});

it('resets any validation error when closing the modal', function (): void {
    $server = Server::factory()->create();

    Livewire::actingAs($server->user)
        ->test(Bip38PasswordModal::class)
        ->emit('askForBip38Password', [
            'start',
            'relay',
            $server->id,
        ])
        ->set('bip38Password', '')
        ->call('submit')
        ->assertHasErrors('bip38Password')
        ->call('closeModal')
        ->assertHasNoErrors();
});

it('resets validation error when password change', function (): void {
    $server = Server::factory()->create();

    Livewire::actingAs($server->user)
        ->test(Bip38PasswordModal::class)
        ->emit('askForBip38Password', [
            'start',
            'relay',
            $server->id,
        ])
        ->set('bip38Password', '')
        ->call('submit')
        ->assertHasErrors('bip38Password')
        ->set('bip38Password', 'a')
        ->assertHasNoErrors();
});

it('triggers the `triggerServerAction` event when submitted', function (): void {
    $server = Server::factory()->create();

    Livewire::actingAs($server->user)
        ->test(Bip38PasswordModal::class)
        ->emit('askForBip38Password', [
            'start',
            'relay',
            $server->id,
        ])
        ->set('bip38Password', 'bar')
        ->call('submit')
        ->assertEmitted('triggerServerAction', [
            'start',
            'relay',
            $server->id,
            ['args' => '--password \'bar\''],
        ])
        ->assertSet('modalShown', false);
});

it('doesnt escapes double quote', function (): void {
    $server = Server::factory()->create();

    Livewire::actingAs($server->user)
        ->test(Bip38PasswordModal::class)
        ->emit('askForBip38Password', [
            'start',
            'relay',
            $server->id,
        ])
        ->set('bip38Password', 'passw"ord')
        ->call('submit')
        ->assertEmitted('triggerServerAction', [
            'start',
            'relay',
            $server->id,
            ['args' => '--password \'passw"ord\''],
        ])
        ->assertSet('modalShown', false);
});

it('doesnt escape backslash', function (): void {
    $server = Server::factory()->create();

    Livewire::actingAs($server->user)
        ->test(Bip38PasswordModal::class)
        ->emit('askForBip38Password', [
            'start',
            'relay',
            $server->id,
        ])
        ->set('bip38Password', 'with\slash')
        ->call('submit')
        ->assertEmitted('triggerServerAction', [
            'start',
            'relay',
            $server->id,
            ['args' => "--password 'with\slash'"],
        ])
        ->assertSet('modalShown', false);
});
