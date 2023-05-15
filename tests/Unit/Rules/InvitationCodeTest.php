<?php

declare(strict_types=1);

use App\Models\InvitationCode as InvitationCodeModel;
use App\Rules\InvitationCode;

beforeEach(fn () => $this->rule = new InvitationCode());

it('should fail if the code is already redeemed', function (): void {
    $model = InvitationCodeModel::factory()->redeemed()->create(['code' => 'redeemed-code']);
    session()->put('username', $model->username);

    expect($this->rule->passes('code', 'redeemed-code'))->toBeFalse();
    expect($this->rule->message())->toBe(trans('validation.invitation_code.redeemed'));
});

it('should fail if the code is not found', function (): void {
    $model = InvitationCodeModel::factory()->create();
    session()->put('username', $model->username);

    expect($this->rule->passes('code', 'not-found-code'))->toBeFalse();
    expect($this->rule->message())->toBe(trans('validation.invitation_code.not_found'));
});

it('should success if code and username are correct', function (): void {
    $model = InvitationCodeModel::factory()->create();
    session()->put('username', $model->username);

    expect($this->rule->passes('code', $model->code))->toBeTrue();
});
