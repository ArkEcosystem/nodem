<?php

declare(strict_types=1);

use App\Enums\ServerProcessTypeEnum;
use App\Enums\TeamMemberRole;
use App\Http\Livewire\ImportServers;
use App\Jobs\PingServer;
use App\Jobs\UpdateServer;
use App\Models\Server;
use App\Models\User;
use App\ViewModels\ServerViewModel;
use Carbon\Carbon;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

function exportServersData($servers)
{
    return collect($servers)->sortBy('name')->map(fn ($server): array => [
        'provider'              => $server->provider,
        'name'                  => $server->name,
        'host'                  => $server->host,
        'process_type'          => ServerProcessTypeEnum::SEPARATE,
        'uses_bip38_encryption' => true,
        'auth'                  => $server->usesAccessKey() ? [
            'access_key' => $server->auth_access_key,
        ] : array_filter([
            'username' => $server->auth_username,
            'password' => $server->auth_password,
        ]),
    ])->toArray();
}

function formatServersData(User $user, array $servers, Carbon $date)
{
    $formattedServers = [];

    foreach ($servers as $server) {
        $formattedServers[] = [
            'user_id'               => $user->id,
            'provider'              => $server['provider'],
            'name'                  => $server['name'],
            'host'                  => rtrim($server['host'], '/'),
            'process_type'          => ServerProcessTypeEnum::SEPARATE,
            'uses_bip38_encryption' => true,
            'auth_username'         => $server['auth']['username'] ?? null,
            'auth_password'         => $server['auth']['password'] ?? null,
            'auth_access_key'       => $server['auth']['access_key'] ?? null,
            'updated_at'            => $date,
            'exists'                => false,
        ];
    }

    return $formattedServers;
}

beforeEach(function () {
    Http::fake();
});

it('can upload a valid json file and read its content', function () {
    Bus::fake();

    $this->seed(AccessControlSeeder::class);

    $this->actingAs($user = User::factory()->create());

    expect(Server::count())->toBe(0);

    $servers[] = $server = Server::factory()->authUsingAccessKey()->make(['user_id' => $user->id, 'name' => 'A', 'provider' => 'aws']);
    $servers[] = Server::factory()->authUsingAccessKey()->make(['user_id' => $user->id, 'name' => 'B', 'provider' => 'linode']);
    $servers[] = Server::factory()->authUsingCredentials()->make(['user_id' => $user->id, 'name' => 'C', 'provider' => 'vultr']);

    $jsonRaw = exportServersData($servers);
    $file    = UploadedFile::fake()->createWithContent('test.json', json_encode($jsonRaw));

    $this->travelTo($date = Carbon::now());

    $expectedFormattedServers = formatServersData($user, $jsonRaw, $date);

    Livewire::test(ImportServers::class)
        ->assertSet('currentStep', 1)
        ->set('jsonFile', $file)
        ->assertSet('filename', 'test.json')
        ->assertSet('currentStep', 2)
        ->assertHasNoErrors()
        ->assertSet('servers', $expectedFormattedServers)
        ->assertSet('selectedServers', [])
        ->call('selectServer', 0)
        ->assertSet('selectedServers', [0])
        ->call('goToNextStep')
        ->call('redirectHome')
        ->assertRedirect(route('home'));

    expect(Server::count())->toBe(1);

    Bus::assertDispatched(UpdateServer::class, 1);
});

it('can handle hosts with trailing slashes', function () {
    Bus::fake();

    $this->seed(AccessControlSeeder::class);

    $this->actingAs($user = User::factory()->create());

    expect(Server::count())->toBe(0);

    $servers = [
        Server::factory()->authUsingAccessKey()->make([
            'user_id'  => $user->id,
            'name'     => 'A',
            'provider' => 'aws',
            'host'     => 'http://1.1.1.1:5001',
        ]),
        Server::factory()->authUsingAccessKey()->make([
            'user_id'  => $user->id,
            'name'     => 'B',
            'provider' => 'linode',
            'host'     => 'http://1.1.1.1:5001/',
        ]),
        Server::factory()->authUsingCredentials()->make([
            'user_id'  => $user->id,
            'name'     => 'C',
            'provider' => 'vultr',
            'host'     => 'http://2.2.2.2:5001/',
        ]),
    ];

    $jsonRaw = exportServersData($servers);
    $file    = UploadedFile::fake()->createWithContent('test.json', json_encode($jsonRaw));

    $this->travelTo($date = Carbon::now());

    $expectedFormattedServers              = formatServersData($user, $jsonRaw, $date);
    $expectedFormattedServers[1]['exists'] = true;

    Livewire::test(ImportServers::class)
        ->assertSet('currentStep', 1)
        ->set('jsonFile', $file)
        ->assertSet('filename', 'test.json')
        ->assertSet('currentStep', 2)
        ->assertHasNoErrors()
        ->assertSet('servers', $expectedFormattedServers)
        ->assertSet('selectedServers', [])
        ->call('selectServer', 0)
        ->call('selectServer', 2)
        ->assertSet('selectedServers', [0, 2])
        ->call('goToNextStep')
        ->call('redirectHome')
        ->assertRedirect(route('home'));

    expect(Server::count())->toBe(2);

    Bus::assertDispatched(UpdateServer::class, 2);
});

it('calls the job for ping the server after imported', function () {
    Bus::fake();

    $this->seed(AccessControlSeeder::class);

    $this->actingAs($user = User::factory()->create());

    $server = Server::factory()->make(['user_id' => $user->id]);

    $jsonRaw = exportServersData([$server]);

    $file = UploadedFile::fake()->createWithContent('test.json', json_encode($jsonRaw));

    Livewire::test(ImportServers::class)
        ->assertSet('currentStep', 1)
        ->set('jsonFile', $file)
        ->assertSet('filename', 'test.json')
        ->assertSet('currentStep', 2);

    Bus::assertDispatched(PingServer::class, fn ($job) => $server->host === $job->host);
});

it('doesnt call the job for ping the server if already exists', function () {
    Bus::fake();

    $this->seed(AccessControlSeeder::class);

    $this->actingAs($user = User::factory()->create());

    $server = Server::factory()->create(['user_id' => $user->id]);

    $jsonRaw = exportServersData([$server]);

    $file = UploadedFile::fake()->createWithContent('test.json', json_encode($jsonRaw));

    Livewire::test(ImportServers::class)
        ->assertSet('currentStep', 1)
        ->set('jsonFile', $file)
        ->assertSet('filename', 'test.json')
        ->assertSet('currentStep', 2);

    Bus::assertNotDispatched(PingServer::class);
});

it('shows an error for unreachable servers', function () {
    $this->seed(AccessControlSeeder::class);

    $this->actingAs($user = User::factory()->create());

    $server = Server::factory()->make(['user_id' => $user->id]);

    $jsonRaw = exportServersData([$server]);

    $file = UploadedFile::fake()->createWithContent('test.json', json_encode($jsonRaw));

    $component = Livewire::test(ImportServers::class)
        ->assertSet('currentStep', 1)
        ->set('jsonFile', $file)
        ->assertSet('filename', 'test.json')
        ->assertSet('currentStep', 2)
        ->assertDontSee(trans('pages.import-servers.manage-import.messages.cannot_connect_to_server'));

    // Emulates that the ping job failed
    Cache::set('ping-'.$server->host, false);

    // Update state
    $component
        ->call('updatePingState')
        ->assertSee(trans('pages.import-servers.manage-import.messages.cannot_connect_to_server'));
});

it('calls the job for ping a server with error after retry', function () {
    $this->seed(AccessControlSeeder::class);

    $this->actingAs($user = User::factory()->create());

    $server  = Server::factory()->make(['user_id' => $user->id]);
    $server2 = Server::factory()->make(['user_id' => $user->id]);

    $jsonRaw = exportServersData([$server, $server2]);

    $file = UploadedFile::fake()->createWithContent('test.json', json_encode($jsonRaw));

    $component = Livewire::test(ImportServers::class)
        ->assertSet('currentStep', 1)
        ->set('jsonFile', $file)
        ->assertSet('filename', 'test.json')
        ->assertSet('currentStep', 2);

    Bus::fake();

    // Emulates that the ping job failed
    Cache::set('ping-'.$server->host, false);

    $component->call('retry');

    Bus::assertDispatched(PingServer::class, fn ($job) => $server->host === $job->host);
    Bus::assertNotDispatched(PingServer::class, fn ($job) => $server2->host === $job->host);
});

it('detects duplicated server on the database', function () {
    $this->seed(AccessControlSeeder::class);

    $this->actingAs($user = User::factory()->create());

    // First factory uses `create` (so already exists)
    $servers[] = $existingServer = Server::factory()->create([
        'user_id' => $user->id,
        'host'    => 'http://10.10.10.10:4005/api',
    ]);
    // The following factories uses `make` (so they dont exists yet)
    $servers[] = Server::factory()->make([
        'user_id' => $user->id,
        'host'    => 'http://20.20.20.20:4005/api',
    ]);
    $servers[] = Server::factory()->make([
        'user_id' => $user->id,
        'host'    => 'http://30.30.30.30:4005/api',
    ]);

    $jsonRaw = exportServersData($servers);
    $file    = UploadedFile::fake()->createWithContent('test.json', json_encode($jsonRaw));

    $this->travelTo($date = Carbon::now());

    $expectedFormattedServers = formatServersData($user, $jsonRaw, $date);
    $existingServerIndex      = collect($expectedFormattedServers)
        ->search(fn ($server) => $server['host'] === 'http://10.10.10.10:4005/api');
    $expectedFormattedServers[$existingServerIndex]['exists'] = true;

    Livewire::test(ImportServers::class)
        ->assertSet('currentStep', 1)
        ->set('jsonFile', $file)
        ->assertSet('servers', $expectedFormattedServers)
        ->assertSee(trans('pages.import-servers.manage-import.messages.duplicated_server'));
});

it('detects duplicated server on the import list', function () {
    $this->seed(AccessControlSeeder::class);

    $this->actingAs($user = User::factory()->create());

    $servers[] = Server::factory()->make([
        'user_id' => $user->id,
        'host'    => 'http://10.10.10.10:4005/api',
    ]);
    $servers[] = Server::factory()->make([
        'user_id' => $user->id,
        'host'    => 'http://10.10.10.10:4005/api',
    ]);
    $servers[] = Server::factory()->make([
        'user_id' => $user->id,
        'host'    => 'http://30.30.30.30:4005/api',
    ]);

    $jsonRaw = exportServersData($servers);
    $file    = UploadedFile::fake()->createWithContent('test.json', json_encode($jsonRaw));

    $this->travelTo($date = Carbon::now());

    $expectedFormattedServers = formatServersData($user, $jsonRaw, $date);
    $existingServerIndex      = collect($expectedFormattedServers)
        // Reverses the array because I will mark as repeated the second ocurrence
        ->reverse()
        ->search(fn ($server) => $server['host'] === 'http://10.10.10.10:4005/api');
    $expectedFormattedServers[$existingServerIndex]['exists'] = true;

    Livewire::test(ImportServers::class)
        ->assertSet('currentStep', 1)
        ->set('jsonFile', $file)
        ->assertSet('servers', $expectedFormattedServers)
        ->assertSee(trans('pages.import-servers.manage-import.messages.duplicated_server'));
});

it('updates the servers array with the updatePingState method', function () {
    $this->seed(AccessControlSeeder::class);

    $this->actingAs($user = User::factory()->create());

    $servers = Server::factory(3)->make(['user_id' => $user->id]);

    $jsonRaw = exportServersData($servers);
    $file    = UploadedFile::fake()->createWithContent('test.json', json_encode($jsonRaw));

    $this->travelTo($date = Carbon::now());

    $expectedFormattedServers = formatServersData($user, $jsonRaw, $date);

    $component = Livewire::test(ImportServers::class)
        ->assertSet('currentStep', 1)
        ->set('jsonFile', $file)
        ->assertSet('filename', 'test.json')
        ->assertSet('currentStep', 2)
        ->assertHasNoErrors()
        ->assertSet('servers', $expectedFormattedServers);

    $this->travelTo($date = Carbon::now()->addMinute());

    $component->call('updatePingState')
        ->assertSet('servers', formatServersData($user, $jsonRaw, $date));
});

it('can handle individual and group select', function () {
    $this->seed(AccessControlSeeder::class);

    $this->actingAs($user = User::factory()->create());

    $servers[] = Server::factory()->authUsingAccessKey()->make(['user_id' => $user->id, 'name' => 'A', 'provider' => 'aws']);
    $servers[] = Server::factory()->authUsingAccessKey()->make(['user_id' => $user->id, 'name' => 'B', 'provider' => 'linode']);
    $servers[] = Server::factory()->authUsingCredentials()->make(['user_id' => $user->id, 'name' => 'C', 'provider' => 'vultr']);

    $jsonRaw = exportServersData($servers);

    $file = UploadedFile::fake()->createWithContent('test.json', json_encode($jsonRaw));

    $this->travelTo($date = Carbon::now());

    $expectedFormattedServers = formatServersData($user, $jsonRaw, $date);

    $component = Livewire::test(ImportServers::class)
        ->assertSet('currentStep', 1)
        ->set('jsonFile', $file)
        ->assertSet('filename', 'test.json')
        ->assertSet('currentStep', 2)
        ->assertHasNoErrors()
        ->assertSet('servers', $expectedFormattedServers)
        ->assertSet('selectedServers', [])
        ->call('selectServer', 0) // Index 0 is the first server
        ->assertSet('selectedServers', [0]);

    expect($component->instance()->hasAllServersSelected())->toBeFalse();
    expect($component->instance()->isSelected(0))->toBeTrue();

    $component
        ->call('selectServer', 1) // Index 1 is the second server
        ->assertSet('selectedServers', [0, 1]);

    expect($component->instance()->hasAllServersSelected())->toBeFalse();
    expect($component->instance()->isSelected(1))->toBeTrue();

    $component
        ->call('selectServer', 2) // Index 2 is the third server
        ->assertSet('selectedServers', [0, 1, 2]);

    expect($component->instance()->hasAllServersSelected())->toBeTrue();
    expect($component->instance()->isSelected(2))->toBeTrue();

    $component
        ->call('selectServer', 2)
        ->assertSet('selectedServers', [0, 1]);

    expect($component->instance()->hasAllServersSelected())->toBeFalse();
    expect($component->instance()->isSelected(2))->toBeFalse();

    $component
        ->call('toggleAllServers')
        ->assertSet('selectedServers', [0, 1, 2])
        ->call('toggleAllServers')
        ->assertSet('selectedServers', []);
});

it('can remove the current json file', function () {
    $this->seed(AccessControlSeeder::class);

    $this->actingAs(User::factory()->create());

    $file = UploadedFile::fake()->createWithContent('test.json', '[{"provider":"aws","name":"ad","host":"http:\/\/38.208.188.208:4005\/api","auth":{"access_key":"WldsrimQE8NgRzFRPdNB3Jx6R6LIBnrz"}}]');

    Livewire::test(ImportServers::class)
        ->set('jsonFile', $file)
        ->assertSet('filename', 'test.json')
        ->call('removeJsonFile')
        ->assertSet('jsonFile', null)
        ->assertSet('filename', null);
});

it('assigns the server to user if owner if not set', function () {
    $this->seed(AccessControlSeeder::class);

    $user = User::factory()->create();

    $server = Server::factory()->make();

    $jsonRaw = exportServersData([$server]);

    $file = UploadedFile::fake()->createWithContent('test.json', json_encode($jsonRaw));

    Livewire::actingAs($user)
        ->test(ImportServers::class)
        ->set('jsonFile', $file)
        ->assertSet('servers.0.user_id', $user->id);
});

it('assigns the server to the owner if set', function () {
    $this->seed(AccessControlSeeder::class);

    $owner = User::factory()->create();

    $user = tap(User::factory()->create())->joinAs(TeamMemberRole::ADMIN, $owner);

    $server = Server::factory()->make();

    $jsonRaw = exportServersData([$server]);

    $file = UploadedFile::fake()->createWithContent('test.json', json_encode($jsonRaw));

    Livewire::actingAs($user)
        ->test(ImportServers::class)
        ->set('jsonFile', $file)
        ->assertSet('servers.0.user_id', $owner->id);
});

it('can reset the wizard process', function () {
    $this->seed(AccessControlSeeder::class);

    $this->actingAs(User::factory()->create());

    $file = UploadedFile::fake()->createWithContent('test.json', '[{"provider":"aws","name":"ad","host":"http:\/\/38.208.188.208:4005\/api","process_type":"separate","uses_bip38_encryption":true,"auth":{"access_key":"WldsrimQE8NgRzFRPdNB3Jx6R6LIBnrz"}}]');

    Livewire::test(ImportServers::class)
        ->set('jsonFile', $file)
        ->assertSet('filename', 'test.json')
        ->assertSet('currentStep', 2)
        ->assertSeeHtml(trans('pages.import-servers.manage-import.title'))
        ->call('resetWizard')
        ->assertSet('jsonFile', null)
        ->assertSet('filename', null)
        ->assertSet('currentStep', 1)
        ->assertSet('servers', [])
        ->assertSet('selectedServers', [])
        ->assertHasNoErrors()
        ->assertDispatchedBrowserEvent('reset-wizard')
        ->assertSeeHtml(trans('pages.import-servers.title'));
});

it('should fail validation if the file is not a proper json file', function () {
    $this->seed(AccessControlSeeder::class);

    $this->actingAs(User::factory()->create());

    $file = UploadedFile::fake()->createWithContent('test.txt', 'fooBar');

    Livewire::test(ImportServers::class)
        ->set('jsonFile', $file)
        ->assertHasErrors(['jsonFile' => 'mimetypes'])
        ->assertSee('test.txt');
});

it('should fail validation if the file contains invalid json', function () {
    $this->seed(AccessControlSeeder::class);

    $this->actingAs(User::factory()->create());

    $file = UploadedFile::fake()->createWithContent('test.json', '[{"provider":"linode","name":"ad","host":"http:\/\/38.208.188.208:4005\/api"]');

    Livewire::test(ImportServers::class)
        ->set('jsonFile', $file)
        ->assertHasErrors(['jsonFile'])
        ->assertSee('test.json')
        ->assertSeeHtml(trans('validation.messages.with_invalid_json'))
        ->assertDispatchedBrowserEvent('validation-error');
});

it('should fail validation if the json contains unexpected property', function () {
    $this->seed(AccessControlSeeder::class);

    $this->actingAs(User::factory()->create());

    $file = UploadedFile::fake()->createWithContent('test.json', '[{"foo":"linode","name":"ad","host":"http:\/\/38.208.188.208:4005\/api","process_type":"separate","uses_bip38_encryption":true,"auth":{"access_key":"WldsrimQE8NgRzFRPdNB3Jx6R6LIBnrz"}}]');

    Livewire::test(ImportServers::class)
        ->set('jsonFile', $file)
        ->assertHasErrors(['jsonFile'])
        ->assertSee('test.json')
        ->assertSeeHtml(trans('validation.messages.with_unexpected_property'))
        ->assertDispatchedBrowserEvent('validation-error');
});

it('should fail validation if the json properties does not match the expected ones', function () {
    $this->seed(AccessControlSeeder::class);

    $this->actingAs(User::factory()->create());

    // One less
    $file1 = UploadedFile::fake()->createWithContent('test1.json', '[{"name":"ad","host":"http:\/\/38.208.188.208:4005\/api","process_type":"separate","uses_bip38_encryption":true,"auth":{"access_key":"WldsrimQE8NgRzFRPdNB3Jx6R6LIBnrz"}}]');
    // One more
    $file2 = UploadedFile::fake()->createWithContent('test2.json', '[{"foo":"bar","provider":"linode", "name":"ad","host":"http:\/\/38.208.188.208:4005\/api","auth":{"access_key":"WldsrimQE8NgRzFRPdNB3Jx6R6LIBnrz"}}]');

    Livewire::test(ImportServers::class)
        ->set('jsonFile', $file1)
        ->assertHasErrors(['jsonFile'])
        ->assertSee('test1.json')
        ->assertSeeHtml(trans('validation.messages.with_missing_property'))
        ->assertDispatchedBrowserEvent('validation-error')
        ->set('jsonFile', $file2)
        ->assertHasErrors(['jsonFile'])
        ->assertSee('test2.json')
        ->assertSeeHtml(trans('validation.messages.with_missing_property'))
        ->assertDispatchedBrowserEvent('validation-error');
});

it('should be able to navigate between steps', function () {
    $this->seed(AccessControlSeeder::class);

    $this->actingAs(User::factory()->create());

    $component = Livewire::test(ImportServers::class);
    $component->assertSet('currentStep', 1);
    $component->instance()->goToNextStep();
    $component->assertSet('currentStep', 2);
    $component->instance()->goToPreviousStep();
});

it('should transform servers into temporary models before saving it', function () {
    Bus::fake();

    $this->seed(AccessControlSeeder::class);

    $this->actingAs(User::factory()->create());

    $file = UploadedFile::fake()->createWithContent('test.json', '[{"provider": "linode", "name":"foo","host":"http:\/\/38.208.188.208:4005\/api","process_type":"separate","uses_bip38_encryption":true,"auth":{"access_key":"WldsrimQE8NgRzFRPdNB3Jx6R6LIBnrz"}}]');

    $component = Livewire::test(ImportServers::class)
        ->set('jsonFile', $file);

    expect($component->instance()->servers[0])->toBeArray();

    expect($component->instance()->getTemporaryServersProperty())->toBeInstanceOf(Collection::class);

    $firstTemporaryServerModel = $component->instance()->getTemporaryServersProperty()->first();
    expect($firstTemporaryServerModel)->toBeInstanceOf(ServerViewModel::class);

    $this->assertDatabaseMissing('servers', [
        'name' => $firstTemporaryServerModel->name(),
    ]);

    $component->call('selectServer', 0);

    expect($component->instance()->selectedServers)->toBeArray();
    expect($component->instance()->selectedServers[0])->toBeInt();

    expect($component->instance()->getTemporarySelectedServersProperty())->toBeArray();

    $firstTemporarySelectedServerModel = $component->instance()->getTemporarySelectedServersProperty()[0];
    expect($firstTemporarySelectedServerModel)->toBeInstanceOf(ServerViewModel::class);

    $this->assertDatabaseMissing('servers', [
        'name' => $firstTemporarySelectedServerModel->name(),
    ]);

    $component->set('currentStep', 2)
        ->call('goToNextStep');

    $this->assertDatabaseHas('servers', [
        'name' => $firstTemporarySelectedServerModel->name(),
    ]);
});

it('can handle selecting when multiple servers share the same name without conflicts', function () {
    $this->seed(AccessControlSeeder::class);

    $this->actingAs(User::factory()->create());

    // Two servers with the same names but different hosts
    $file = UploadedFile::fake()->createWithContent('test.json', '[{"provider": "linode", "name":"foo","host":"http:\/\/38.208.188.208:4005\/api","process_type":"separate","uses_bip38_encryption":true,"auth":{"access_key":"WldsrimQE8NgRzFRPdNB3Jx6R6LIBnrz"}},{"provider": "linode", "name":"foo","host":"http:\/\/42.127.144.206:4005\/api","process_type":"separate","uses_bip38_encryption":true,"auth":{"access_key":"WldsrimQE8NgRzFRPdNB3Jx6R6LIBnrz"}}]');

    $component = Livewire::test(ImportServers::class)
        ->assertSet('currentStep', 1)
        ->set('jsonFile', $file)
        ->assertSet('filename', 'test.json')
        ->assertSet('currentStep', 2)
        ->assertHasNoErrors()
        ->assertSet('selectedServers', [])
        ->call('selectServer', 0)
        ->assertSet('selectedServers', [0]);

    expect($component->instance()->hasAllServersSelected())->toBeFalse();
    expect($component->instance()->isSelected(0))->toBeTrue();
    expect($component->instance()->isSelected(1))->toBeFalse();
});

it('cannot import servers without the right permissions', function () {
    $this->seed(AccessControlSeeder::class);

    $owner = User::factory()->create();

    $this->actingAs($user = User::factory()->create());

    $user->joinAs(TeamMemberRole::READONLY, $owner);

    Livewire::actingAs($user)
        ->test(ImportServers::class)
        ->assertForbidden();
});
