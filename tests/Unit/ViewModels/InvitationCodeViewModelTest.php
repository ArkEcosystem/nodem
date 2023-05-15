<?php

declare(strict_types=1);

use App\Models\InvitationCode;
use App\ViewModels\InvitationCodeViewModel;

it('should get the id', function () {
    $subject = new InvitationCodeViewModel($invite = InvitationCode::factory()->create());

    expect($subject->id())->toBe($invite->id);
});

it('should get the username', function () {
    $subject = new InvitationCodeViewModel($invite = InvitationCode::factory()->create());

    expect($subject->username())->toBe($invite->username);
});

it('should get the issuer', function () {
    $subject = new InvitationCodeViewModel($invite = InvitationCode::factory()->create());

    expect($subject->issuer()->id)->toBe($invite->issuer_id);
});

it('should get the role', function () {
    $subject = new InvitationCodeViewModel($invite = InvitationCode::factory()->create());

    expect($subject->role())->toBe($invite->role);
});

it('should get the code', function () {
    $subject = new InvitationCodeViewModel($invite = InvitationCode::factory()->create());

    expect($subject->code())->toBe($invite->code);
});

it('should get the date generated', function () {
    $subject = new InvitationCodeViewModel($invite = InvitationCode::factory()->create());

    expect($subject->dateGeneratedString())->toBe($invite->created_at->format('d M Y'));
});
