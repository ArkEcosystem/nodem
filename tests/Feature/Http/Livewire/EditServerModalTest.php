<?php

declare(strict_types=1);

use App\Enums\ServerProcessTypeEnum;
use App\Enums\ServerProviderTypeEnum;
use App\Enums\TeamMemberPermission;
use App\Enums\TeamMemberRole;
use App\Http\Livewire\EditServerModal;
use App\Models\Server;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use function Tests\createServerWithFixture;
use function Tests\createServerWithFixtureSequence;

it('should show the modal', function (): void {
    $user = User::factory()->create();

    $server = Server::factory()->create(['user_id' => $user->id]);

    $component = Livewire::actingAs($user)->test(EditServerModal::class);
    $component->call('open', $server->id);
    $component->assertSet('modalShown', true);
    $component->assertSet('server', fn ($property) => $property->is($server));
});

it('should hide the modal', function (): void {
    $user = User::factory()->create();

    $component = Livewire::actingAs($user)->test(EditServerModal::class);
    $component->call('closeModal');
    $component->assertSet('modalShown', false);
    $component->assertSet('server', null);
});

it('should reset any validation error when closing the modal', function (): void {
    $user = User::factory()->create();

    $server = Server::factory()->create(['name' => 'foo', 'user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(EditServerModal::class)
        ->call('open', $server->id)
        ->set('state.name', 'a')
        ->assertHasErrors()
        ->call('onModalClosed')
        ->assertSet('modalShown', false)
        ->assertSet('server', null)
        ->assertHasNoErrors();
});

it('should clear a validation error when the issue is resolved', function (): void {
    $user = User::factory()->create();

    $server = Server::factory()->create(['name' => 'foo', 'user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(EditServerModal::class)
        ->call('open', $server->id)
        ->set('state.name', 'a')
        ->assertHasErrors()
        ->set('state.name', 'aaa')
        ->assertHasNoErrors();
});

it('should not be possible to edit a server without permission', function () {
    $this->seed(AccessControlSeeder::class);

    $owner = User::factory()->create();
    $user  = User::factory()->create();
    $user->joinAs(TeamMemberRole::MAINTAINER, $owner);

    expect($user->can(TeamMemberPermission::SERVER_EDIT))->toBeFalse();

    Livewire::actingAs($user)
        ->test(EditServerModal::class)
        ->call('editServer')
        ->assertForbidden();
});

it('should correctly validate if host already exists in the database', function (): void {
    $owner = User::factory()->create();

    Server::factory()->create(['host' => 'http://1.1.1.1:4005']);

    $server = Server::factory()->create([
        'user_id'  => $owner->id,
        'host'     => 'http://2.2.2.2:4005',
        'provider' => ServerProviderTypeEnum::DIGITAL_OCEAN,
    ]);

    Livewire::actingAs($owner)
        ->test(EditServerModal::class)
        ->call('open', $server->id)
        ->assertHasNoErrors('state.host')
        ->set('state.host', 'http://1.1.1.1:4005')
        ->assertHasErrors('state.host')
        ->set('state.host', 'http://2.2.2.2:4005')
        ->assertHasNoErrors('state.host');
});

it('should set the available providers on initialization of the component', function () {
    $this->seed(AccessControlSeeder::class);

    $owner = User::factory()->create();

    $server = Server::factory()->create(['user_id' => $owner->id]);

    expect($owner->can(TeamMemberPermission::SERVER_EDIT))->toBeTrue();

    Livewire::actingAs($owner)
        ->test(EditServerModal::class)
        ->call('open', $server->id)
        ->assertSet('providers', ServerProviderTypeEnum::toArray());
});

it('should be able to edit an existing server', function () {
    Queue::fake();

    $this->seed(AccessControlSeeder::class);

    $owner = User::factory()->create();

    $server = Server::factory()->create([
        'user_id'  => $owner->id,
        'provider' => ServerProviderTypeEnum::DIGITAL_OCEAN,
        'name'     => 'my-server',
    ]);

    $contents = json_decode(file_get_contents(base_path('tests/fixtures/process/list.json')), true);

    Http::fake([
        'https://10.10.10.10:4040/api' => Http::response($contents, 200),
    ]);

    expect($owner->can(TeamMemberPermission::SERVER_EDIT))->toBeTrue();

    $component = Livewire::actingAs($owner)
        ->test(EditServerModal::class)
        ->assertSet('useCredentials', false)
        ->call('open', $server->id)
        ->assertSet('server', fn ($property) => $property->is($server))
        ->assertSet('useCredentials', true)
        ->assertSet('serverCheckingError', false)
        ->assertSet('providers', ServerProviderTypeEnum::toArray());

    expect($component->instance()->canSubmit())->toBeFalse();

    $component
        ->set('state.provider', ServerProviderTypeEnum::LINODE)
        ->set('state.name', 'fooBar')
        ->set('state.host', 'https://10.10.10.10:4040/api')
        ->assertHasNoErrors();

    expect($component->instance()->canSubmit())->toBeTrue();

    $this->assertDatabaseHas('servers', [
        'id'   => $server->id,
        'name' => $server->name,
    ]);

    $this->assertDatabaseMissing('servers', [
        'id'   => $server->id,
        'name' => 'fooBar',
    ]);

    $component
        ->call('editServer')
        ->assertSet('serverCheckingError', false);

    $this->assertDatabaseMissing('servers', [
        'id'   => $server->id,
        'name' => $server->name,
    ]);

    $this->assertDatabaseHas('servers', [
        'id'   => $server->id,
        'name' => 'fooBar',
    ]);

    $component
        ->assertSet('modalShown', false)
        ->assertSet('server', null)
        ->assertSet('useCredentials', false)
        ->assertRedirect(route('home'));
});

it('should be able to edit a single property of an existing server', function () {
    Queue::fake();

    $this->seed(AccessControlSeeder::class);

    $owner  = User::factory()->create();
    $server = Server::factory()->create([
        'user_id'  => $owner->id,
        'provider' => ServerProviderTypeEnum::DIGITAL_OCEAN,
        'host'     => 'https://10.10.10.10:4040/api',
    ]);
    $contents = json_decode(file_get_contents(base_path('tests/fixtures/process/list.json')), true);

    Http::fake([
        'https://10.10.10.10:4040/api' => Http::response($contents, 200),
    ]);

    expect($owner->can(TeamMemberPermission::SERVER_EDIT))->toBeTrue();

    Livewire::actingAs($owner)
        ->test(EditServerModal::class)
        ->assertSet('useCredentials', false)
        ->call('open', $server->id)
        ->assertSet('server', fn ($property) => $property->is($server))
        ->assertSet('useCredentials', true)
        ->assertSet('serverCheckingError', false)
        ->assertSet('providers', ServerProviderTypeEnum::toArray())
        ->set('state.name', 'fooBar')
        ->assertHasNoErrors()
        ->call('editServer')
        ->assertSet('serverCheckingError', false)
        ->assertSet('modalShown', false)
        ->assertSet('server', null)
        ->assertSet('useCredentials', false)
        ->assertRedirect(route('home'));

    $this->assertDatabaseHas('servers', [
        'id'   => $server->id,
        'name' => 'fooBar',
    ]);
});

it('should stop the process if the server is not reachable', function () {
    Http::fake([
        'https://10.10.10.10:4040/api' => function () {
            throw new ConnectionException();
        },
    ]);

    $this->seed(AccessControlSeeder::class);

    $owner = User::factory()->create();

    $server = Server::factory()->create(['user_id' => $owner->id, 'provider' => ServerProviderTypeEnum::DIGITAL_OCEAN]);

    Livewire::actingAs($owner)
        ->test(EditServerModal::class)
        ->call('open', $server->id)
        ->assertSet('serverCheckingError', false)
        ->set('useCredentials', true)
        ->set('state.provider', 'hetzner')
        ->set('state.name', 'My Server')
        ->set('state.host', 'https://10.10.10.10:4040/api')
        ->set('state.auth_username', 'username')
        ->set('state.auth_password', 'password')
        ->set('state.auth_access_key', null)
        ->call('editServer')
        ->assertSet('serverCheckingError', true);
});

it('should show an error message if the server credentials are not correct', function () {
    Queue::fake();
    $this->seed(AccessControlSeeder::class);

    $owner = User::factory()->create();

    $server = tap(createServerWithFixture('error/credentials'))->update([
        'user_id'  => $owner->id,
        'provider' => ServerProviderTypeEnum::DIGITAL_OCEAN,
        'name'     => 'dummy-server',
    ]);

    $server->setMetaAttribute('server_is_online', true);

    Livewire::actingAs($owner)
        ->test(EditServerModal::class)
        ->call('open', $server->id)
        ->assertSet('serverCheckingError', false)
        ->set('useCredentials', true)
        ->set('state.provider', 'hetzner')
        ->set('state.name', 'My Server')
        ->set('state.host', 'https://mynode.com')
        ->set('state.auth_username', 'username')
        ->set('state.auth_password', 'password')
        ->set('state.auth_access_key', null)
        ->call('editServer')
        ->assertSet('serverCheckingError', true);

    expect($server->name)->toBe('dummy-server');
});

it('should show an error message if the server is not inline with the preference', function () {
    Queue::fake();
    $this->seed(AccessControlSeeder::class);

    $owner = User::factory()->create();

    $server = tap(createServerWithFixtureSequence('info/coreVersion', 'process/list_core'))->update([
        'user_id'  => $owner->id,
        'provider' => ServerProviderTypeEnum::DIGITAL_OCEAN,
        'name'     => 'dummy-server',
    ]);

    $server->setMetaAttribute('server_is_online', true);

    Livewire::actingAs($owner)
        ->test(EditServerModal::class)
        ->call('open', $server->id)
        ->assertSet('serverCheckingError', false)
        ->set('useCredentials', true)
        ->set('state.provider', 'hetzner')
        ->set('state.name', 'My state')
        ->set('state.host', 'https://mynode.com')
        ->set('state.auth_username', 'username')
        ->set('state.auth_password', 'password')
        ->set('state.auth_access_key', null)
        ->call('editServer')
        ->assertSet('serverCheckingError', true)
        ->assertSet('serverCheckingErrorMessage', trans('pages.add-server-modal.process_type.separate_server_error'));

    expect($server->name)->toBe('dummy-server');
});

it('stores the server host without trailing slashes', function (): void {
    Queue::fake();

    $this->seed(AccessControlSeeder::class);

    $owner  = User::factory()->create();
    $server = Server::factory()->create([
        'user_id'  => $owner->id,
        'provider' => ServerProviderTypeEnum::DIGITAL_OCEAN,
        'host'     => 'https://10.10.10.10:4040',
    ]);
    $contents = json_decode(file_get_contents(base_path('tests/fixtures/process/list.json')), true);

    Http::fake([
        'https://10.10.10.10:4040' => Http::response($contents, 200),
    ]);

    expect($owner->can(TeamMemberPermission::SERVER_EDIT))->toBeTrue();

    Livewire::actingAs($owner)
        ->test(EditServerModal::class)
        ->assertSet('useCredentials', false)
        ->call('open', $server->id)
        ->assertSet('server', fn ($property) => $property->is($server))
        ->assertSet('useCredentials', true)
        ->assertSet('serverCheckingError', false)
        ->assertSet('providers', ServerProviderTypeEnum::toArray())
        ->set('state.name', 'fooBar')
        ->set('state.host', 'https://10.10.10.10:4040/')
        ->assertHasNoErrors()
        ->call('editServer')
        ->assertSet('serverCheckingError', false)
        ->assertSet('modalShown', false)
        ->assertSet('server', null)
        ->assertSet('useCredentials', false)
        ->assertRedirect(route('home'));

    $this->assertDatabaseHas('servers', [
        'host' => 'https://10.10.10.10:4040',
    ]);

    $this->assertDatabaseMissing('servers', [
        'host' => 'https://10.10.10.10:4040/',
    ]);
});

it('doesnt accept a combined process type if server only has separated processes', function () {
    Queue::fake();

    $owner  = User::factory()->create();
    $server = tap(createServerWithFixtureSequence('info/coreVersion', 'info/coreVersion', 'process/list'))->update([
        'user_id'       => $owner->id,
        'provider'      => ServerProviderTypeEnum::DIGITAL_OCEAN,
        'name'          => 'dummy-server',
        'auth_username' => null,
        'auth_password' => null,
        'process_type'  => ServerProcessTypeEnum::SEPARATE,
    ]);

    $server->setMetaAttribute('server_is_online', true);

    expect($owner->can(TeamMemberPermission::SERVER_EDIT))->toBeTrue();

    Livewire::actingAs($owner)->test(EditServerModal::class)
        ->assertSet('serverCheckingError', false)
        ->call('open', $server->id)
        ->set('useCredentials', false)
        ->assertSet('useCredentials', false)
        ->set('state.provider', 'hetzner')
        ->set('state.name', 'My Server')
        ->set('state.host', 'http://mynode.com')
        ->set('state.process_type', ServerProcessTypeEnum::COMBINED)
        ->set('state.auth_username', null)
        ->set('state.auth_password', null)
        ->set('state.auth_access_key', 'valid_token')
        ->call('editServer')
        ->assertSet('serverCheckingError', true)
        ->assertSet('serverCheckingErrorMessage', trans('pages.add-server-modal.process_type.combined_server_error'));

    $this->assertDatabaseHas('servers', [
        'id'           => $server->id,
        'name'         => 'dummy-server',
        'process_type' => ServerProcessTypeEnum::SEPARATE,
    ]);
});

it('accept a separated process type if server only has separated processes', function () {
    Queue::fake();

    $this->seed(AccessControlSeeder::class);
    $owner  = User::factory()->create();
    $server = tap(createServerWithFixtureSequence('info/coreVersion', 'info/coreVersion', 'process/list'))->update([
        'user_id'       => $owner->id,
        'provider'      => ServerProviderTypeEnum::DIGITAL_OCEAN,
        'name'          => 'dummy-server',
        'auth_username' => null,
        'auth_password' => null,
        'process_type'  => ServerProcessTypeEnum::COMBINED,
    ]);

    $server->setMetaAttribute('server_is_online', true);

    expect($owner->can(TeamMemberPermission::SERVER_EDIT))->toBeTrue();

    Livewire::actingAs($owner)->test(EditServerModal::class)
        ->assertSet('serverCheckingError', false)
        ->set('useCredentials', false)
        ->call('open', $server->id)
        ->assertSet('useCredentials', false)
        ->set('state.provider', 'hetzner')
        ->set('state.name', 'My Server')
        ->set('state.host', 'http://mynode.com')
        ->set('state.process_type', ServerProcessTypeEnum::SEPARATE)
        ->set('state.auth_username', null)
        ->set('state.auth_password', null)
        ->set('state.auth_access_key', 'valid_token')
        ->call('editServer')
        ->assertSet('serverCheckingError', false)
        ->assertSet('serverCheckingErrorMessage', null);

    $this->assertDatabaseHas('servers', [
        'id'           => $server->id,
        'name'         => 'My Server',
        'process_type' => ServerProcessTypeEnum::SEPARATE,
    ]);
});
