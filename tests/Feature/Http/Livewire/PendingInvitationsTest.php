<?php

declare(strict_types=1);

use App\Enums\TeamMemberRole;
use App\Http\Livewire\PendingInvitations;
use App\Models\InvitationCode;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\AccessControlSeeder;
use Livewire\Livewire;

it('should not render if no invites', function (): void {
    $user = User::factory()->create();

    $component = Livewire::test(PendingInvitations::class)
        ->assertDontSee('Pending Invitations');
});

it('should render if invites', function (): void {
    $user = User::factory()->create();

    InvitationCode::factory(8)->create();

    $component = Livewire::test(PendingInvitations::class)
        ->assertSee('Pending Invitations');
});

it('should populate computed invites property', function (): void {
    $user = User::factory()->create();

    InvitationCode::factory(8)->create();

    $instance = Livewire::actingAs($user)
        ->test(PendingInvitations::class)
        ->instance();

    expect($instance->getInvitesProperty()->count())->toBe(8);
});

it('should open and close delete component modal', function (): void {
    $user = User::factory()->create();

    InvitationCode::factory()->create(['code' => 'invite-code']);

    Livewire::actingAs($user)
        ->test(PendingInvitations::class)
        ->assertSet('modalShown', false)
        ->call('openDeleteInvitationCode', 'invite-code')
        ->assertSet('modalShown', true)
        ->assertSet('selectedInvitationCode', 'invite-code')
        ->assertSee(trans('pages.team.pending_invitations.delete_modal.title'))
        ->assertSee(trans('pages.team.pending_invitations.delete_modal.description'))
        ->call('closeDeleteInvitationCode')
        ->assertSet('modalShown', false)
        ->assertNotSet('selectedInvitationCode', 'invite-code')
        ->assertDontSee(trans('pages.team.pending_invitations.delete_modal.title'))
        ->assertDontSee(trans('pages.team.pending_invitations.delete_modal.description'));
});

it('should not open delete component modal if invite does not exist', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(PendingInvitations::class)
        ->call('openDeleteInvitationCode', 'invalid-code')
        ->assertNotSet('selectedInvitationCode', 'invalid-code')
        ->assertDontSee(trans('pages.team.pending_invitations.delete_modal.title'))
        ->assertDontSee(trans('pages.team.pending_invitations.delete_modal.description'));
});

it('should delete invitation code & close modal', function (): void {
    $user = User::factory()->create();

    InvitationCode::factory(7)->create();
    InvitationCode::factory()->create(['code' => 'invite-code']);

    expect(InvitationCode::count())->toBe(8);

    Livewire::actingAs($user)
        ->test(PendingInvitations::class)
        ->call('openDeleteInvitationCode', 'invite-code')
        ->assertSee(trans('pages.team.pending_invitations.delete_modal.title'))
        ->call('deleteInvitationCode')
        ->assertDontSee(trans('pages.team.pending_invitations.delete_modal.title'));

    expect(InvitationCode::count())->toBe(7);
});

it('should not delete invite if current user does not have permission', function (): void {
    $this->seed(AccessControlSeeder::class);

    $owner = User::factory()->create();
    $user  = User::factory()->create();
    $user->joinAs(TeamMemberRole::MAINTAINER, $owner);

    InvitationCode::factory(7)->create();
    InvitationCode::factory()->create(['code' => 'invite-code']);

    expect(InvitationCode::count())->toBe(8);

    Livewire::actingAs($user)
        ->test(PendingInvitations::class)
        ->call('openDeleteInvitationCode', 'invite-code')
        ->assertSee(trans('pages.team.pending_invitations.delete_modal.title'))
        ->call('deleteInvitationCode')
        ->assertForbidden();

    expect(InvitationCode::count())->toBe(8);
});

it('should only display pending invitations', function (): void {
    $this->seed(AccessControlSeeder::class);

    $owner = User::factory()->create();
    $user  = User::factory()->create();
    $user->joinAs(TeamMemberRole::MAINTAINER, $owner);

    $firstInvitation  = InvitationCode::factory()->create(['code' => 'invite-code-1']);
    $secondInvitation = InvitationCode::factory()->create(['code' => 'invite-code-2']);

    expect(InvitationCode::count())->toBe(2);

    $component = Livewire::actingAs($owner)
        ->test(PendingInvitations::class)
        ->assertSee($firstInvitation->code)
        ->assertSee($secondInvitation->code);

    $firstInvitation->update(['redeemed_at' => Carbon::now()]);

    $component = Livewire::actingAs($owner)
        ->test(PendingInvitations::class)
        ->assertDontSee($firstInvitation->code)
        ->assertSee($secondInvitation->code);

    $secondInvitation->update(['redeemed_at' => Carbon::now()]);

    $component = Livewire::actingAs($owner)
        ->test(PendingInvitations::class)
        ->assertDontSee($firstInvitation->code)
        ->assertDontSee($secondInvitation->code);

    expect(InvitationCode::count())->toBe(2);
});
