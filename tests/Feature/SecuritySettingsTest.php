<?php

declare(strict_types=1);

use App\Models\User;

it('passes even if 2fa is not enabled', function () {
    enableTwoFactorGuard();

    $user = User::factory()->create([
        'two_factor_secret'         => null,
        'two_factor_recovery_codes' => null,
    ]);

    $this->actingAs($user)
        ->get(route('account.settings.password'))
        ->assertOk();
});

it('passes if 2fa is enabled', function () {
    enableTwoFactorGuard();

    $user = User::factory()->create([
        'two_factor_secret'         => 'some-code',
        'two_factor_recovery_codes' => json_encode(['some-code']),
    ]);

    $this->actingAs($user)
        ->get(route('account.settings.password'))
        ->assertOk();
});
