<?php

declare(strict_types=1);

use App\Http\Livewire\RegisterForm;
use App\Models\InvitationCode;
use Livewire\Livewire;

beforeEach(fn () => $this->invitation = InvitationCode::factory()->create());

it('can not sign up if username is not provided', function (): void {
    $instance = Livewire::test(RegisterForm::class)
        ->set('code', $this->invitation->code)
        ->set('password', 'Sup3rStr0ngP4ssw0rd!')
        ->set('password_confirmation', 'Sup3rStr0ngP4ssw0rd!')
        ->instance();

    expect($instance->canSubmit())->toBeFalse();
});

it('can not sign up if username does not match', function (): void {
    $instance = Livewire::test(RegisterForm::class)
        ->set('username', 'not-matching-username')
        ->set('code', $this->invitation->code)
        ->set('password', 'Sup3rStr0ngP4ssw0rd!')
        ->set('password_confirmation', 'Sup3rStr0ngP4ssw0rd!')
        ->instance();

    expect($instance->canSubmit())->toBeFalse();
});

it('can not sign up if invitation code is not provided', function (): void {
    $instance = Livewire::test(RegisterForm::class)
        ->set('username', $this->invitation->username)
        ->set('password', 'Sup3rStr0ngP4ssw0rd!')
        ->set('password_confirmation', 'Sup3rStr0ngP4ssw0rd!')
        ->instance();

    expect($instance->canSubmit())->toBeFalse();
});

it('can not sign up if invitation code is not found', function (): void {
    $instance = Livewire::test(RegisterForm::class)
        ->set('username', $this->invitation->username)
        ->set('code', 'not-found-code')
        ->set('password', 'Sup3rStr0ngP4ssw0rd!')
        ->set('password_confirmation', 'Sup3rStr0ngP4ssw0rd!')
        ->instance();

    expect($instance->canSubmit())->toBeFalse();
});

it('can not sign up if invitation code is redeemed', function (): void {
    $invitation = InvitationCode::factory()->redeemed()->create();

    $instance = Livewire::test(RegisterForm::class)
        ->set('username', $invitation->username)
        ->set('code', $invitation->code)
        ->set('password', 'Sup3rStr0ngP4ssw0rd!')
        ->set('password_confirmation', 'Sup3rStr0ngP4ssw0rd!')
        ->instance();

    expect($instance->canSubmit())->toBeFalse();
});

it('can not sign up if password is not confirmed', function (): void {
    $instance = Livewire::test(RegisterForm::class)
        ->set('username', $this->invitation->username)
        ->set('code', 'not-found-code')
        ->set('password', 'Sup3rStr0ngP4ssw0rd!')
        ->instance();

    expect($instance->canSubmit())->toBeFalse();
});

it('can not sign up if password confirmation does not match', function (): void {
    $instance = Livewire::test(RegisterForm::class)
        ->set('username', $this->invitation->username)
        ->set('code', 'not-found-code')
        ->set('password', 'Sup3rStr0ngP4ssw0rd!')
        ->set('password_confirmation', 'P4ssw0rd!Sup3rStr0ng')
        ->instance();

    expect($instance->canSubmit())->toBeFalse();
});

it('can not sign up if terms are not accepted', function (): void {
    $instance = Livewire::test(RegisterForm::class)
        ->set('code', $this->invitation->code)
        ->set('username', $this->invitation->username)
        ->set('password', 'Sup3rStr0ngP4ssw0rd!')
        ->set('password_confirmation', 'Sup3rStr0ngP4ssw0rd!')
        ->assertSet('terms', false)
        ->instance();

    expect($instance->canSubmit())->toBeFalse();
});

it('can sign up', function (): void {
    $instance = Livewire::test(RegisterForm::class)
        ->set('code', $this->invitation->code)
        ->set('username', $this->invitation->username)
        ->set('password', 'Sup3rStr0ngP4ssw0rd!')
        ->set('password_confirmation', 'Sup3rStr0ngP4ssw0rd!')
        ->set('terms', true)
        ->instance();

    expect($instance->canSubmit())->toBeTrue();
});
