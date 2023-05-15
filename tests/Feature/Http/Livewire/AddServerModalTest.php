<?php

declare(strict_types=1);

use App\Enums\ServerProcessTypeEnum;
use App\Http\Livewire\AddServerModal;
use App\Jobs\UpdateServer;
use App\Models\Server;
use App\Models\User;
use App\Services\Client\Exceptions\RPCResponseException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use function Tests\createServerWithFixture;
use function Tests\createServerWithFixtureSequence;

it('should show the modal', function (): void {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)->test(AddServerModal::class);
    $component->call('openModal');
    $component->assertSet('modalShown', true);
});

it('should hide the modal', function (): void {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)->test(AddServerModal::class);
    $component->call('closeModal');
    $component->assertSet('modalShown', false);
});

it('should add the server with basic auth and dispatch jobs to gather data', function (): void {
    createServerWithFixtureSequence('info/coreVersion', 'info/coreVersion', 'process/list');

    Queue::fake();

    $user = User::factory()->create();

    $component = Livewire::actingAs($user)->test(AddServerModal::class);
    $component->set('useCredentials', true);
    $component->set('state.provider', 'hetzner');
    $component->set('state.name', 'My New Server');
    $component->set('state.host', 'http://mynode.com');
    $component->set('state.process_type', ServerProcessTypeEnum::SEPARATE);
    $component->set('state.auth_username', 'username');
    $component->set('state.auth_password', 'password');
    $component->set('state.auth_access_key', null);
    $component->call('addServer');
    $component->assertHasNoErrors();
    $component->assertSet('serverCheckingError', false);
    $component->assertEmitted('serverAdded');
    $component->assertEmitted('modalClosed');
    $component->call('onModalClosed');

    Queue::assertPushed(UpdateServer::class, 1);

    $component->assertSet('state.provider', null);
});

it('should add the server with token auth and dispatch jobs to gather data', function (): void {
    createServerWithFixtureSequence('info/coreVersion', 'info/coreVersion', 'process/list');

    Queue::fake();

    $user = User::factory()->create();

    $component = Livewire::actingAs($user)->test(AddServerModal::class);
    $component->set('useCredentials', false);
    $component->set('state.provider', 'hetzner');
    $component->set('state.name', 'My Server');
    $component->set('state.host', 'http://mynode.com');
    $component->set('state.process_type', ServerProcessTypeEnum::SEPARATE);
    $component->set('state.auth_username', null);
    $component->set('state.auth_password', null);
    $component->set('state.auth_access_key', 'secret_token');
    $component->call('addServer');
    $component->assertHasNoErrors();
    $component->assertSet('serverCheckingError', false);
    $component->assertEmitted('serverAdded');
    $component->assertEmitted('modalClosed');
    $component->call('onModalClosed');

    Queue::assertPushed(UpdateServer::class, 1);

    $component->assertSet('state.provider', null);
});

it('stores the server with basic auth and bip38 encryption', function (): void {
    createServerWithFixtureSequence('info/coreVersion', 'info/coreVersion', 'process/list');

    Queue::fake();

    $user = User::factory()->create();

    $component = Livewire::actingAs($user)->test(AddServerModal::class);
    $component->set('useCredentials', true);
    $component->set('state.provider', 'hetzner');
    $component->set('state.name', 'My Server');
    $component->set('state.host', 'http://mynode.com');
    $component->set('state.process_type', ServerProcessTypeEnum::SEPARATE);
    $component->set('state.auth_username', 'username');
    $component->set('state.auth_password', 'password');
    $component->set('state.auth_access_key', null);
    $component->set('state.uses_bip38_encryption', true);
    $component->call('addServer');
    $component->assertHasNoErrors();
    $component->assertSet('serverCheckingError', false);
    $component->assertEmitted('serverAdded');
    $component->assertEmitted('modalClosed');
    $component->call('onModalClosed');

    $server = Server::whereName('My Server')->first();

    expect($server->user_id)->toBe($user->id);
    expect($server->provider)->toBe('hetzner');
    expect($server->name)->toBe('My Server');
    expect($server->host)->toBe('http://mynode.com');
    expect($server->uses_bip38_encryption)->toBe(true);
    expect($server->auth_username)->toBe('username');
    expect($server->auth_password)->toBe('password');
    expect($server->auth_access_key)->toBeNull();
});

it('stores the server with token auth and without bip38 encryption', function (): void {
    createServerWithFixtureSequence('info/coreVersion', 'info/coreVersion', 'process/list');

    Queue::fake();

    $user = User::factory()->create();

    $component = Livewire::actingAs($user)->test(AddServerModal::class);
    $component->set('useCredentials', false);
    $component->set('state.provider', 'hetzner');
    $component->set('state.name', 'My Server');
    $component->set('state.host', 'http://mynode.com');
    $component->set('state.process_type', ServerProcessTypeEnum::SEPARATE);
    $component->set('state.auth_username', null);
    $component->set('state.auth_password', null);
    $component->set('state.auth_access_key', 'secret_token');
    $component->set('state.uses_bip38_encryption', false);
    $component->call('addServer');
    $component->assertHasNoErrors();
    $component->assertSet('serverCheckingError', false);
    $component->assertEmitted('serverAdded');
    $component->assertEmitted('modalClosed');
    $component->call('onModalClosed');

    $server = Server::whereName('My Server')->first();

    expect($server->user_id)->toBe($user->id);
    expect($server->provider)->toBe('hetzner');
    expect($server->name)->toBe('My Server');
    expect($server->host)->toBe('http://mynode.com');
    expect($server->uses_bip38_encryption)->toBe(false);
    expect($server->auth_username)->toBeNull();
    expect($server->auth_password)->toBeNull();
    expect($server->auth_access_key)->toBe('secret_token');
});

it('stores the server host without trailing slashes', function (): void {
    createServerWithFixtureSequence('info/coreVersion', 'info/coreVersion', 'process/list');

    Queue::fake();

    $user = User::factory()->create();

    $component = Livewire::actingAs($user)
        ->test(AddServerModal::class)
        ->set('useCredentials', true)
        ->set('state.provider', 'hetzner')
        ->set('state.name', 'My Server')
        ->set('state.host', 'http://mynode.com/')
        ->set('state.process_type', ServerProcessTypeEnum::SEPARATE)
        ->set('state.auth_username', 'username')
        ->set('state.auth_password', 'password')
        ->set('state.auth_access_key', null)
        ->set('state.uses_bip38_encryption', true)
        ->call('addServer')
        ->assertHasNoErrors()
        ->assertSet('serverCheckingError', false)
        ->assertEmitted('serverAdded')
        ->assertEmitted('modalClosed')
        ->call('onModalClosed');

    $server = Server::whereName('My Server')->first();

    expect($server->user_id)->toBe($user->id);
    expect($server->provider)->toBe('hetzner');
    expect($server->name)->toBe('My Server');
    expect($server->host)->toBe('http://mynode.com');
    expect($server->uses_bip38_encryption)->toBe(true);
    expect($server->auth_username)->toBe('username');
    expect($server->auth_password)->toBe('password');
    expect($server->auth_access_key)->toBeNull();
});

it('shows a validation error if name is too short', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(AddServerModal::class)
        ->set('useCredentials', true)
        ->set('state.provider', 'hetzner')
        ->set('state.name', 'S')
        ->set('state.host', 'http://127.0.0.1:4005')
        ->set('state.auth_username', 'username')
        ->set('state.auth_password', 'password')
        ->set('state.auth_access_key', null)
        ->call('addServer')
        ->assertHasErrors('state.name');
});

it('shows a validation error if name is too long', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(AddServerModal::class)
        ->set('useCredentials', true)
        ->set('state.provider', 'hetzner')
        ->set('state.name', 'Server name too long to make validation passing')
        ->set('state.host', 'http://127.0.0.1:4005')
        ->set('state.auth_username', 'username')
        ->set('state.auth_password', 'password')
        ->set('state.auth_access_key', null)
        ->call('addServer')
        ->assertHasErrors('state.name');
});

it('shows a validation error if host contains port number but it is not an ip address', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(AddServerModal::class)
        ->set('useCredentials', true)
        ->set('state.provider', 'hetzner')
        ->set('state.name', 'My Server')
        ->set('state.host', 'http://domain.com:4005')
        ->set('state.auth_username', 'username')
        ->set('state.auth_password', 'password')
        ->set('state.auth_access_key', null)
        ->call('addServer')
        ->assertHasErrors('state.host');
});

it('shows a validation error if host already exists in the database', function (): void {
    $user = User::factory()->create();

    Server::factory()->create(['host' => 'http://1.1.1.1:4005']);

    Livewire::actingAs($user)
        ->test(AddServerModal::class)
        ->set('useCredentials', true)
        ->set('state.provider', 'hetzner')
        ->set('state.name', 'My Server')
        ->set('state.host', 'http://2.2.2.2:4005')
        ->set('state.auth_username', 'username')
        ->set('state.auth_password', 'password')
        ->set('state.auth_access_key', null)
        ->call('addServer')
        ->assertHasNoErrors('state.host')
        ->set('state.host', 'http://1.1.1.1:4005')
        ->call('addServer')
        ->assertHasErrors('state.host');
});

it('does not show a validation error if host has trailing slashes but already exists', function (): void {
    $user = User::factory()->create();

    Server::factory()->create(['host' => 'http://1.1.1.1:4005']);

    Livewire::actingAs($user)
        ->test(AddServerModal::class)
        ->set('useCredentials', true)
        ->set('state.provider', 'hetzner')
        ->set('state.name', 'My Server')
        ->set('state.host', 'http://1.1.1.1:4005/')
        ->set('state.auth_username', 'username')
        ->set('state.auth_password', 'password')
        ->set('state.auth_access_key', null)
        ->call('addServer')
        ->assertHasNoErrors('state.host')
        ->call('addServer')
        ->assertHasNoErrors('state.host');
});

it('cannot submit if all required fields and access key are not filled', function () {
    $instance = Livewire::test(AddServerModal::class);

    expect($instance->instance()->canSubmit())->toBeFalse();

    $instance->set('state.provider', 'aws');
    expect($instance->instance()->canSubmit())->toBeFalse();

    $instance->set('state.name', 'My Node');
    expect($instance->instance()->canSubmit())->toBeFalse();

    $instance->set('state.host', 'http://127.0.0.1:4005');
    expect($instance->instance()->canSubmit())->toBeFalse();

    $instance->set('state.process_type', ServerProcessTypeEnum::SEPARATE);
    expect($instance->instance()->canSubmit())->toBeFalse();

    $instance->set('useCredentials', false);
    expect($instance->instance()->canSubmit())->toBeFalse();

    $instance->set('state.auth_access_key', '1234567890');
    expect($instance->instance()->canSubmit())->toBeTrue();
});

it('cannot submit if all required fields and username and password are not filled', function () {
    $instance = Livewire::test(AddServerModal::class);

    expect($instance->instance()->canSubmit())->toBeFalse();

    $instance->set('state.provider', 'aws');
    expect($instance->instance()->canSubmit())->toBeFalse();

    $instance->set('state.name', 'My Node');
    expect($instance->instance()->canSubmit())->toBeFalse();

    $instance->set('state.host', 'http://127.0.0.1:4005');
    expect($instance->instance()->canSubmit())->toBeFalse();

    $instance->set('state.process_type', ServerProcessTypeEnum::SEPARATE);
    expect($instance->instance()->canSubmit())->toBeFalse();

    $instance->set('useCredentials', true);
    expect($instance->instance()->canSubmit())->toBeFalse();

    $instance->set('state.auth_username', 'username');
    expect($instance->instance()->canSubmit())->toBeFalse();

    $instance->set('state.auth_password', 'password');
    expect($instance->instance()->canSubmit())->toBeTrue();
});

it('should show an error message if the server credentials are not correct', function () {
    createServerWithFixture('error/credentials');

    Queue::fake();

    $user = User::factory()->create();

    $component = Livewire::actingAs($user)->test(AddServerModal::class);
    $component->assertSet('serverCheckingError', false);
    $component->set('useCredentials', false);
    $component->set('state.provider', 'hetzner');
    $component->set('state.name', 'My Server');
    $component->set('state.host', 'http://mynode.com');
    $component->set('state.process_type', ServerProcessTypeEnum::SEPARATE);
    $component->set('state.auth_username', null);
    $component->set('state.auth_password', null);
    $component->set('state.auth_access_key', 'invalid_token');
    $component->call('addServer');
    $component->assertNotEmitted('serverAdded');
    $component->assertNotEmitted('modalClosed');
    $component->assertSet('serverCheckingError', true);

    Queue::assertNotPushed(UpdateServer::class, 1);
});

it('should show an error message if the server is unavailable', function () {
    Http::fake(function () {
        throw new RPCResponseException();
    });

    Queue::fake();

    $user = User::factory()->create();

    $component = Livewire::actingAs($user)->test(AddServerModal::class);
    $component->assertSet('serverCheckingError', false);
    $component->set('useCredentials', false);
    $component->set('state.provider', 'hetzner');
    $component->set('state.name', 'My Server');
    $component->set('state.host', 'http://foo.bar.com');
    $component->set('state.process_type', ServerProcessTypeEnum::SEPARATE);
    $component->set('state.auth_username', null);
    $component->set('state.auth_password', null);
    $component->set('state.auth_access_key', 'invalid_token');
    $component->call('addServer');
    $component->assertNotEmitted('serverAdded');
    $component->assertNotEmitted('modalClosed');
    $component->assertSet('serverCheckingError', true);

    Queue::assertNotPushed(UpdateServer::class, 1);
});

it('can handle credentials mode changes', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(AddServerModal::class)
        ->set('useCredentials', true)
        ->set('state.provider', 'hetzner')
        ->set('state.name', 'My Server')
        ->set('state.host', 'http://127.0.0.1:4005')
        ->set('state.process_type', ServerProcessTypeEnum::SEPARATE)
        ->set('state.auth_username', 'username')
        ->set('state.auth_password', 'password')
        ->set('state.auth_access_key', 'token')
        ->call('credentialsModeChanged')
        ->assertSet('state.auth_username', null)
        ->assertSet('state.auth_password', null)
        ->assertSet('state.auth_access_key', null);
});

it('doesnt accept a combined process type if server only has separated processes', function () {
    createServerWithFixtureSequence('info/coreVersion', 'info/coreVersion', 'process/list');

    Queue::fake();

    $user = User::factory()->create();

    $component = Livewire::actingAs($user)->test(AddServerModal::class);
    $component->assertSet('serverCheckingError', false);
    $component->set('useCredentials', false);
    $component->set('state.provider', 'hetzner');
    $component->set('state.name', 'My Server');
    $component->set('state.host', 'http://mynode.com');
    $component->set('state.process_type', ServerProcessTypeEnum::COMBINED);
    $component->set('state.auth_username', null);
    $component->set('state.auth_password', null);
    $component->set('state.auth_access_key', 'invalid_token');
    $component->call('addServer');
    $component->assertNotEmitted('serverAdded');
    $component->assertNotEmitted('modalClosed');
    $component->assertSet('serverCheckingError', true);
    $component->assertSet('serverCheckingErrorMessage', trans('pages.add-server-modal.process_type.combined_server_error'));

    Queue::assertNotPushed(UpdateServer::class, 1);
});

it('doesnt accepts a separated process type if server only has a core process', function () {
    createServerWithFixtureSequence('info/coreVersion', 'info/coreVersion', 'process/list_core');

    Queue::fake();

    $user = User::factory()->create();

    $component = Livewire::actingAs($user)->test(AddServerModal::class);
    $component->assertSet('serverCheckingError', false);
    $component->set('useCredentials', false);
    $component->set('state.provider', 'hetzner');
    $component->set('state.name', 'My Server');
    $component->set('state.host', 'http://mynode.com');
    $component->set('state.process_type', ServerProcessTypeEnum::SEPARATE);
    $component->set('state.auth_username', null);
    $component->set('state.auth_password', null);
    $component->set('state.auth_access_key', 'invalid_token');
    $component->call('addServer');
    $component->assertNotEmitted('serverAdded');
    $component->assertNotEmitted('modalClosed');
    $component->assertSet('serverCheckingError', true);
    $component->assertSet('serverCheckingErrorMessage', trans('pages.add-server-modal.process_type.separate_server_error'));

    Queue::assertNotPushed(UpdateServer::class, 1);
});

it('accepts separate process type if no processes', function () {
    createServerWithFixtureSequence('info/coreVersion', 'info/coreVersion', 'process/list_empty');

    Queue::fake();

    $user = User::factory()->create();

    $component = Livewire::actingAs($user)->test(AddServerModal::class);
    $component->set('useCredentials', false);
    $component->set('state.provider', 'hetzner');
    $component->set('state.name', 'My Server');
    $component->set('state.host', 'http://mynode.com');
    $component->set('state.process_type', ServerProcessTypeEnum::SEPARATE);
    $component->set('state.auth_username', null);
    $component->set('state.auth_password', null);
    $component->set('state.auth_access_key', 'invalid_token');
    $component->call('addServer');
    $component->assertEmitted('serverAdded');
    $component->assertEmitted('modalClosed');
    $component->assertSet('serverCheckingError', false);
    $component->assertSet('serverCheckingErrorMessage', null);

    Queue::assertPushed(UpdateServer::class, 1);
});

it('accepts combined process type if no processes', function () {
    createServerWithFixtureSequence('info/coreVersion', 'info/coreVersion', 'process/list_empty');

    Queue::fake();

    $user = User::factory()->create();

    $component = Livewire::actingAs($user)->test(AddServerModal::class);
    $component->set('useCredentials', false);
    $component->set('state.provider', 'hetzner');
    $component->set('state.name', 'My Server');
    $component->set('state.host', 'http://mynode.com');
    $component->set('state.process_type', ServerProcessTypeEnum::COMBINED);
    $component->set('state.auth_username', null);
    $component->set('state.auth_password', null);
    $component->set('state.auth_access_key', 'invalid_token');
    $component->call('addServer');
    $component->assertEmitted('serverAdded');
    $component->assertEmitted('modalClosed');
    $component->assertSet('serverCheckingError', false);
    $component->assertSet('serverCheckingErrorMessage', null);

    Queue::assertPushed(UpdateServer::class, 1);
});

it('accepts separated process type when it has a core process but is stopped', function () {
    createServerWithFixtureSequence('info/coreVersion', 'info/coreVersion', 'process/list_offline_core');

    Queue::fake();

    $user = User::factory()->create();

    $component = Livewire::actingAs($user)->test(AddServerModal::class);
    $component->set('useCredentials', false);
    $component->set('state.provider', 'hetzner');
    $component->set('state.name', 'My Server');
    $component->set('state.host', 'http://mynode.com');
    $component->set('state.process_type', ServerProcessTypeEnum::SEPARATE);
    $component->set('state.auth_username', null);
    $component->set('state.auth_password', null);
    $component->set('state.auth_access_key', 'invalid_token');
    $component->call('addServer');
    $component->assertEmitted('serverAdded');
    $component->assertEmitted('modalClosed');
    $component->assertSet('serverCheckingError', false);
    $component->assertSet('serverCheckingErrorMessage', null);

    Queue::assertPushed(UpdateServer::class, 1);
});

it('accepts combined process type when only have separated processes but they are stopped', function () {
    createServerWithFixtureSequence('info/coreVersion', 'info/coreVersion', 'process/list_offline');

    Queue::fake();

    $user = User::factory()->create();

    $component = Livewire::actingAs($user)->test(AddServerModal::class);
    $component->set('useCredentials', false);
    $component->set('state.provider', 'hetzner');
    $component->set('state.name', 'My Server');
    $component->set('state.host', 'http://mynode.com');
    $component->set('state.process_type', ServerProcessTypeEnum::COMBINED);
    $component->set('state.auth_username', null);
    $component->set('state.auth_password', null);
    $component->set('state.auth_access_key', 'invalid_token');
    $component->call('addServer');
    $component->assertEmitted('serverAdded');
    $component->assertEmitted('modalClosed');
    $component->assertSet('serverCheckingError', false);
    $component->assertSet('serverCheckingErrorMessage', null);

    Queue::assertPushed(UpdateServer::class, 1);
});
