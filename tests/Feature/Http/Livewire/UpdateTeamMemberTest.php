<?php

declare(strict_types=1);

use App\Enums\TeamMemberPermission;
use App\Enums\TeamMemberRole;
use App\Http\Livewire\UpdateTeamMember;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(AccessControlSeeder::class);
});

it('can open and close a modal', function (): void {
    $owner = User::factory()->create();

    Role::firstOrCreate(['name' => TeamMemberRole::ADMIN]);

    $user = tap(User::factory()->create(['username' => 'admin']))
        ->joinAs(TeamMemberRole::ADMIN, $owner);

    Livewire::actingAs($owner)
        ->test(UpdateTeamMember::class)
        ->assertSet('member', null)
        ->assertSet('role', null)
        ->call('open', $user->id)
        ->assertSet('member.id', $user->id)
        ->call('close')
        ->assertSet('member', null)
        ->assertSet('role', null);
});

it('can update a team member', function (): void {
    $owner = User::factory()->create();

    Role::firstOrCreate(['name' => TeamMemberRole::MAINTAINER]);
    Role::firstOrCreate(['name' => TeamMemberRole::ADMIN]);

    $user = tap(User::factory()->create(['username' => 'admin']))
        ->joinAs(TeamMemberRole::ADMIN, $owner);

    expect($owner->can(TeamMemberPermission::TEAM_MEMBERS_EDIT))->toBeTrue();
    expect($user->can(TeamMemberPermission::TEAM_MEMBERS_EDIT))->toBeTrue();

    $this->assertDatabaseHas('model_has_roles', [
        'role_id'    => Role::findByName(TeamMemberRole::ADMIN)->id,
        'model_type' => User::class,
        'model_id'   => $user->id,
    ]);

    Livewire::actingAs($owner)
        ->test(UpdateTeamMember::class)
        ->call('open', $user->id)
        ->set('role', 1)
        ->call('save')
        ->assertHasErrors(['role'])
        ->call('close')
        ->call('open', $user->id)
        ->set('role', TeamMemberRole::MAINTAINER)
        ->call('save')
        ->assertHasNoErrors()
        ->assertEmitted('toastMessage', [trans('pages.team.edit-modal.update_success'), 'success'])
        ->assertEmitted('teamMemberUpdated');

    $this->assertDatabaseHas('model_has_roles', [
        'role_id'    => Role::findByName(TeamMemberRole::MAINTAINER)->id,
        'model_type' => User::class,
        'model_id'   => $user->id,
    ]);
});

it('cannot update a team member without the proper permissions', function (): void {
    $owner = User::factory()->create();

    Role::firstOrCreate(['name' => TeamMemberRole::MAINTAINER]);
    Role::firstOrCreate(['name' => TeamMemberRole::READONLY]);

    $user = tap(User::factory()->create(['username' => 'maintainer']))
        ->joinAs(TeamMemberRole::MAINTAINER, $owner);

    $userToUpdate = tap(User::factory()->create(['username' => 'maintainer2']))
        ->joinAs(TeamMemberRole::MAINTAINER, $owner);

    expect($owner->can(TeamMemberPermission::TEAM_MEMBERS_EDIT))->toBeTrue();
    expect($user->can(TeamMemberPermission::TEAM_MEMBERS_EDIT))->toBeFalse();

    $this->assertDatabaseHas('model_has_roles', [
        'role_id'    => Role::findByName(TeamMemberRole::MAINTAINER)->id,
        'model_type' => User::class,
        'model_id'   => $userToUpdate->id,
    ]);

    Livewire::actingAs($user)
        ->test(UpdateTeamMember::class)
        ->call('open', $userToUpdate->id)
        ->set('role', TeamMemberRole::READONLY)
        ->call('save')
        ->assertHasNoErrors()
        ->assertNotEmitted('toastMessage', [trans('pages.team.edit-modal.update_success'), 'success'])
        ->assertNotEmitted('teamMemberUpdated');

    $this->assertDatabaseMissing('model_has_roles', [
        'role_id'    => Role::findByName(TeamMemberRole::READONLY)->id,
        'model_type' => User::class,
        'model_id'   => $userToUpdate->id,
    ]);
});
