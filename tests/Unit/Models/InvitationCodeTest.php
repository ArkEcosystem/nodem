<?php

declare(strict_types=1);

use App\Models\InvitationCode;
use App\Models\User;

it('should have an issuer', function (): void {
    $issuer         = User::factory()->create();
    $invitationCode = InvitationCode::factory()->create(['issuer_id' => $issuer]);

    expect($invitationCode->issuer->is($issuer))->toBeTrue();
});

it('has a scope to search for case insensitive username', function (): void {
    InvitationCode::factory()->create(['username' => 'JohnDoe']);

    expect(InvitationCode::username('johndoe')->exists())->toBeTrue();
});

it('knows if code has been redeemed', function (): void {
    $invitationCode = InvitationCode::factory()->redeemed()->create();

    expect($invitationCode->hasBeenRedeemed())->toBeTrue();
});

it('knows if user has been already invited', function (): void {
    InvitationCode::factory()->create(['username' => 'john.doe']);

    expect(InvitationCode::userHasBeenInvited('john.doe'))->toBeTrue();
});

it('knows if user is already a team member', function (): void {
    InvitationCode::factory()->redeemed()->create(['username' => 'john.doe']);

    expect(InvitationCode::userIsATeamMember('john.doe'))->toBeTrue();
});

it('can find an invitation code by code and username', function (): void {
    InvitationCode::factory(3)->create();
    $invitationCode = InvitationCode::factory()->create([
        'username' => 'john.doe',
        'code'     => '1234',
    ]);

    expect(
        InvitationCode::findByCodeAndUsername('1234', 'john.doe')
            ->is($invitationCode)
    )->toBeTrue();
});
