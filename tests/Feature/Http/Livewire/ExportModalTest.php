<?php

declare(strict_types=1);

use App\Enums\ServerProcessTypeEnum;
use App\Enums\TeamMemberPermission;
use App\Enums\TeamMemberRole;
use App\Http\Livewire\ExportModal;
use App\Models\Server;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Support\Str;
use Livewire\Livewire;

it('can retrieve current user', function () {
    $this->actingAs($user = User::factory()->create());

    Livewire::test(ExportModal::class)
            ->assertSet('user', fn ($model) => $model->is($user));
});

it('contains a modal', function () {
    $this->actingAs($user = User::factory()->create());

    Livewire::test(ExportModal::class)
            ->assertSet('modalShown', false)
            ->call('openModal')
            ->assertSet('modalShown', true)
            ->call('closeModal')
            ->assertSet('modalShown', false)
            ->assertEmitted('modalClosed');
});

test('users without permission cannot export servers', function () {
    $this->seed(AccessControlSeeder::class);

    $owner                = User::factory()->create();
    $this->actingAs($user = User::factory()->create());
    $user->joinAs(TeamMemberRole::MAINTAINER, $owner);

    expect($user->can(TeamMemberPermission::SERVER_CONFIGURATION_EXPORT))->toBeFalse();

    Livewire::test(ExportModal::class)
            ->call('export')
            ->assertForbidden();
});

it('can generate export data', function () {
    $this->seed(AccessControlSeeder::class);

    $owner                = User::factory()->create();
    $this->actingAs($user = User::factory()->create());
    $user->joinAs(TeamMemberRole::ADMIN, $owner);

    expect($user->can(TeamMemberPermission::SERVER_CONFIGURATION_EXPORT))->toBeTrue();

    $first  = Server::factory()->authUsingAccessKey()->prefersSeparated()->usesBip38Encryption()->create(['user_id' => $owner->id, 'name' => 'A', 'provider' => 'aws']);
    $second = Server::factory()->authUsingAccessKey()->prefersSeparated()->create(['user_id' => $owner->id, 'name' => 'C', 'provider' => 'linode']);
    $third  = Server::factory()->authUsingCredentials()->prefersCombined()->create(['user_id' => $owner->id, 'name' => 'B', 'provider' => 'vultr']);

    // Somebody else...
    Server::factory()->authUsingCredentials()->create();

    $response = Livewire::test(ExportModal::class)
            ->call('export')
            ->assertDispatchedBrowserEvent('export-ready')
            ->payload['effects']['download'];

    $this->assertTrue(Str::endsWith($response['name'], '.json'));

    $collection = json_decode(base64_decode($response['content'], true), true);

    expect($collection)->toHaveCount(3);
    expect($collection[0])->toBe([
        'provider'              => 'aws',
        'name'                  => 'A',
        'host'                  => $first->host,
        'process_type'          => ServerProcessTypeEnum::SEPARATE,
        'uses_bip38_encryption' => true,
        'auth'                  => [
            'access_key' => $first->auth_access_key,
        ],
    ]);

    expect($collection[1])->toBe([
        'provider'              => 'vultr',
        'name'                  => 'B',
        'host'                  => $third->host,
        'process_type'          => ServerProcessTypeEnum::COMBINED,
        'uses_bip38_encryption' => false,
        'auth'                  => [
            'username' => $third->auth_username,
            'password' => $third->auth_password,
        ],
    ]);

    expect($collection[2])->toBe([
        'provider'              => 'linode',
        'name'                  => 'C',
        'host'                  => $second->host,
        'process_type'          => ServerProcessTypeEnum::SEPARATE,
        'uses_bip38_encryption' => false,
        'auth'                  => [
            'access_key' => $second->auth_access_key,
        ],
    ]);
});
