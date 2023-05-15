<?php

declare(strict_types=1);

use App\Enums\TeamMemberRole;
use App\Http\Livewire\InviteTeamMember;
use App\Models\InvitationCode;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;
use Livewire\Livewire;

it('can invite a new team member', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(InviteTeamMember::class)
        ->call('open')
        ->assertSee(trans('pages.team.invite_title'))
        ->set('username', 'john.doe')
        ->set('role', TeamMemberRole::ADMIN)
        ->call('invite')
        ->assertHasNoErrors()
        ->tap(function ($instance) {
            $this->assertDatabaseHas(InvitationCode::class, [
                'username' => 'john.doe',
                'role'     => TeamMemberRole::ADMIN,
                'code'     => $instance->code,
            ]);
        })
        ->assertDontSee(trans('pages.team.invite_title'))
        ->assertSee(trans('pages.team.pending_title'))
        ->call('close')
        ->assertDontSee(trans('pages.team.invite_title'))
        ->assertDontSee(trans('pages.team.pending_title'));
});

it('can invite a new team member without caring about the case sensitivity of the username', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(InviteTeamMember::class)
        ->call('open')
        ->assertSee(trans('pages.team.invite_title'))
        ->set('username', 'JOHN.doE')
        ->set('role', TeamMemberRole::ADMIN)
        ->call('invite')
        ->assertHasNoErrors()
        ->tap(function ($instance) {
            $this->assertDatabaseHas(InvitationCode::class, [
                'username' => 'JOHN.doE',
                'role'     => TeamMemberRole::ADMIN,
                'code'     => $instance->code,
            ]);
        })
        ->assertDontSee(trans('pages.team.invite_title'))
        ->assertSee(trans('pages.team.pending_title'))
        ->call('close')
        ->assertDontSee(trans('pages.team.invite_title'))
        ->assertDontSee(trans('pages.team.pending_title'));
});

it('can invite an existing user', function (): void {
    $user  = User::factory()->create();
    $guest = User::factory()->create();

    $instance = Livewire::actingAs($user)
        ->test(InviteTeamMember::class)
        ->call('open')
        ->set('username', $guest->username)
        ->set('role', TeamMemberRole::ADMIN)
        ->call('invite')
        ->assertHasNoErrors();

    $this->assertDatabaseHas(InvitationCode::class, [
        'username' => $guest->username,
        'role'     => TeamMemberRole::ADMIN,
    ]);
});

it('can not invite if username is empty', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(InviteTeamMember::class)
        ->call('open')
        ->set('username', '')
        ->set('role', TeamMemberRole::ADMIN)
        ->call('invite')
        ->assertHasErrors(['username']);
});

it('can not invite if role is empty', function (): void {
    $user  = User::factory()->create();

    Livewire::actingAs($user)
        ->test(InviteTeamMember::class)
        ->call('open')
        ->set('username', 'john.doe')
        ->set('role', '')
        ->call('invite')
        ->assertHasErrors(['role']);
});

it('can not invite if user already invited', function (): void {
    $user   = User::factory()->create();
    $member = User::factory()->create();

    InvitationCode::factory()->create([
        'issuer_id' => $user->id,
        'username'  => $member->username,
    ]);

    Livewire::actingAs($user)
        ->test(InviteTeamMember::class)
        ->call('open')
        ->set('username', $member->username)
        ->set('role', TeamMemberRole::ADMIN)
        ->call('invite')
        ->assertHasErrors(['username']);
});

it('can not invite if user already a member', function (): void {
    $user   = User::factory()->create();
    $member = User::factory()->create();

    InvitationCode::factory()->redeemed()->create([
        'issuer_id' => $user->id,
        'username'  => $member->username,
    ]);

    Livewire::actingAs($user)
        ->test(InviteTeamMember::class)
        ->call('open')
        ->set('username', $member->username)
        ->set('role', TeamMemberRole::ADMIN)
        ->call('invite')
        ->assertHasErrors(['username']);
});

it('can not invite if current user does not have permission', function (): void {
    $this->seed(AccessControlSeeder::class);

    $owner = User::factory()->create();
    $user  = User::factory()->create();
    $user->joinAs(TeamMemberRole::MAINTAINER, $owner);

    Livewire::actingAs($user)
        ->test(InviteTeamMember::class)
        ->call('open')
        ->assertSee(trans('pages.team.invite_title'))
        ->set('username', 'john.doe')
        ->set('role', TeamMemberRole::ADMIN)
        ->call('invite')
        ->assertForbidden();
});

it('should close modal', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(InviteTeamMember::class)
        ->call('open')
        ->set('username', 'john.doe')
        ->set('role', TeamMemberRole::ADMIN)
        ->call('close')
        ->assertSet('modalShown', false)
        ->assertSet('username', null)
        ->assertSet('role', TeamMemberRole::READONLY)
        ->assertDontSee(trans('pages.team.invite_title'));
});

it('should have a default role when inviting a new team member', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(InviteTeamMember::class)
        ->assertSet('role', TeamMemberRole::READONLY);
});
