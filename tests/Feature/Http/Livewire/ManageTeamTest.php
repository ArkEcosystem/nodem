<?php

declare(strict_types=1);

use App\Enums\TeamMemberPermission;
use App\Enums\TeamMemberRole;
use App\Http\Livewire\ManageTeam;
use App\Models\InvitationCode;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(AccessControlSeeder::class);
});

it('should render the component', function (): void {
    $owner = User::factory()->create();

    Role::firstOrCreate(['name' => TeamMemberRole::ADMIN]);

    User::factory()
        ->create(['username' => 'admin'])
        ->joinAs(TeamMemberRole::ADMIN, $owner);

    $component = Livewire::actingAs($owner)
        ->test(ManageTeam::class);

    expect($component->instance()->getTeamMembersProperty())->toBeInstanceOf(LengthAwarePaginator::class);
    expect($component->instance()->getTeamMembersProperty())->toHaveCount(2);
});

it('should show invite button if no pending invites', function (): void {
    $owner = User::factory()->create();

    Role::firstOrCreate(['name' => TeamMemberRole::ADMIN]);
    User::factory()
        ->create(['username' => 'admin'])
        ->joinAs(TeamMemberRole::ADMIN, $owner);

    $component = Livewire::actingAs($owner)
        ->test(ManageTeam::class)
        ->assertSeeHtml('livewire.emit(\'openInviteTeamMember\')');
});

it('should not show invite button if pending invites', function (): void {
    $owner = User::factory()->create();

    Role::firstOrCreate(['name' => TeamMemberRole::ADMIN]);
    User::factory()
        ->create(['username' => 'admin'])
        ->joinAs(TeamMemberRole::ADMIN, $owner);

    InvitationCode::factory(8)->create();

    $component = Livewire::actingAs($owner)
        ->test(ManageTeam::class)
        ->assertDontSeeHtml('livewire.emit(\'openInviteTeamMember\')');
});

it('can open and close a modal', function (): void {
    $owner = User::factory()->create();

    Role::firstOrCreate(['name' => TeamMemberRole::ADMIN]);

    $user = tap(User::factory()->create(['username' => 'admin']))
        ->joinAs(TeamMemberRole::ADMIN, $owner);

    Livewire::actingAs($owner)
        ->test(ManageTeam::class)
        ->assertSet('modalShown', false)
        ->call('openConfirm', $user->id)
        ->assertSet('modalShown', true)
        ->assertSet('removalId', $user->id)
        ->assertSet('selectedTeamMember.id', $user->id)
        ->call('closeConfirm')
        ->assertSet('selectedTeamMember', null)
        ->assertSet('removalId', null);
});

it('can remove a team member', function (): void {
    $owner = User::factory()->create();

    Role::firstOrCreate(['name' => TeamMemberRole::ADMIN]);

    $user = tap(User::factory()->create(['username' => 'admin']))
        ->joinAs(TeamMemberRole::ADMIN, $owner);

    expect($owner->can(TeamMemberPermission::TEAM_MEMBERS_DELETE))->toBeTrue();
    expect($user->can(TeamMemberPermission::TEAM_MEMBERS_DELETE))->toBeTrue();

    expect($user->owners()->count())->toBe(1);
    expect(User::count())->toBe(2);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
    ]);

    Livewire::actingAs($owner)
        ->test(ManageTeam::class)
        ->assertSet('modalShown', false)
        ->call('openConfirm', $user->id)
        ->assertSet('modalShown', true)
        ->assertSet('removalId', $user->id)
        ->assertSet('selectedTeamMember.id', $user->id)
        ->call('remove')
        ->assertEmitted('toastMessage')
        ->assertEmitted('teamMemberRemoved')
        ->assertSet('removalId', null)
        ->assertSet('selectedTeamMember', null);

    expect($user->owners()->count())->toBe(0);
    expect(User::count())->toBe(1);

    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);
});

it('cannot remove a team member without the proper permissions', function (): void {
    $owner = User::factory()->create();

    Role::firstOrCreate(['name' => TeamMemberRole::MAINTAINER]);
    Role::firstOrCreate(['name' => TeamMemberRole::ADMIN]);

    $user = tap(User::factory()->create(['username' => 'maintainer']))
        ->joinAs(TeamMemberRole::MAINTAINER, $owner);

    $userToRemove = tap(User::factory()->create(['username' => 'maintainer2']))
        ->joinAs(TeamMemberRole::MAINTAINER, $owner);

    expect($owner->can(TeamMemberPermission::TEAM_MEMBERS_DELETE))->toBeTrue();
    expect($user->can(TeamMemberPermission::TEAM_MEMBERS_DELETE))->toBeFalse();

    expect($user->owners()->count())->toBe(1);
    expect(User::count())->toBe(3);

    $this->assertDatabaseHas('users', [
        'id' => $userToRemove->id,
    ]);

    Livewire::actingAs($user)
        ->test(ManageTeam::class)
        ->assertSet('modalShown', false)
        ->call('openConfirm', $userToRemove->id)
        ->assertSet('modalShown', true)
        ->assertSet('removalId', $userToRemove->id)
        ->assertSet('selectedTeamMember.id', $userToRemove->id)
        ->call('remove')
        ->assertNotEmitted('toastMessage')
        ->assertNotEmitted('teamMemberRemoved')
        ->call('closeConfirm')
        ->assertSet('removalId', null)
        ->assertSet('selectedTeamMember', null);

    expect($user->owners()->count())->toBe(1);
    expect(User::count())->toBe(3);

    $this->assertDatabaseHas('users', [
        'id' => $userToRemove->id,
    ]);
});
