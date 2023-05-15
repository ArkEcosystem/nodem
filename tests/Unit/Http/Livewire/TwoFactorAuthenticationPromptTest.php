<?php

declare(strict_types=1);

use App\Http\Livewire\TwoFactorAuthenticationPrompt;
use App\Models\User;
use ARKEcosystem\Foundation\Fortify\Actions\EnableTwoFactorAuthentication;
use Livewire\Livewire;
use Livewire\Testing\TestableLivewire;

beforeEach(function () {
    TestableLivewire::macro('assertVisible', fn () => $this->assertSee(trans('pages.user-settings.security.two_factor_prompt.submit')));
    TestableLivewire::macro('assertHidden', fn () => $this->assertDontSee(trans('pages.user-settings.security.two_factor_prompt.submit')));
});

it('can retrieve current user', function () {
    $this->actingAs($user = User::factory()->create());

    Livewire::test(TwoFactorAuthenticationPrompt::class)
            ->assertSet('user', fn ($model) => $model->is($user));
});

it('sets the shown state based on users 2fa state', function () {
    $this->actingAs($user = User::factory()->create());

    Livewire::test(TwoFactorAuthenticationPrompt::class)
            ->assertSet('modalShown', true)
            ->call('dismiss')
            ->assertSet('modalShown', false);

    Livewire::test(TwoFactorAuthenticationPrompt::class)
        ->assertSet('modalShown', true)
        ->call('dismiss')
        ->assertSet('modalShown', false);

    resolve(EnableTwoFactorAuthentication::class)($user, 'secretKey');

    Livewire::test(TwoFactorAuthenticationPrompt::class)
        ->assertSet('modalShown', false)
        ->call('dismiss');
});

it('hides the modal', function () {
    $this->actingAs($user = User::factory()->create());

    Livewire::test(TwoFactorAuthenticationPrompt::class)
            ->assertVisible()
            ->call('dismiss')
            ->assertHidden();
});

it('shows the modal by default', function () {
    $this->actingAs($user = User::factory()->create());

    Livewire::test(TwoFactorAuthenticationPrompt::class)->assertVisible();
});
