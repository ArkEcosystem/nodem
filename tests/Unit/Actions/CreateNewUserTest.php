<?php

declare(strict_types=1);

use App\Actions\CreateNewUser;
use App\Models\InvitationCode;
use App\Models\User;
use Database\Seeders\AccessControlSeeder;

beforeEach(fn () => (new AccessControlSeeder())->run());

it('can create a new user', function (): void {
    $invitation = InvitationCode::factory()->create();

    expect($invitation->fresh()->hasBeenRedeemed())->toBeFalse();

    session()->flash('username', $invitation->username);

    expect(User::count())->toBe(1);

    /** @var User $user */
    $user = (new CreateNewUser())->create([
        'username'              => $invitation->username,
        'code'                  => $invitation->code,
        'password'              => 'SuperStr0ng!',
        'password_confirmation' => 'SuperStr0ng!',
        'terms'                 => true,
    ]);

    $newUser = User::latest('id')->first();

    expect(User::count())->toBe(2);
    expect($newUser->is($user))->toBeTrue();
    expect($newUser->username)->toBe($invitation->username);

    expect($invitation->fresh()->hasBeenRedeemed())->toBeTrue();
});

it('can create a new user using lower and upper case characters', function (): void {
    $invitation = InvitationCode::factory()->create(['username' => 'SaMtHeMaN']);

    expect($invitation->fresh()->hasBeenRedeemed())->toBeFalse();

    session()->flash('username', $invitation->username);

    expect(User::count())->toBe(1);

    /** @var User $user */
    $user = (new CreateNewUser())->create([
        'username'              => $invitation->username,
        'code'                  => $invitation->code,
        'password'              => 'SuperStr0ng!',
        'password_confirmation' => 'SuperStr0ng!',
        'terms'                 => true,
    ]);

    $newUser = User::latest('id')->first();

    expect(User::count())->toBe(2);
    expect($newUser->is($user))->toBeTrue();
    expect($newUser->username)->toBe($invitation->username);
    expect($newUser->username)->toBe('SaMtHeMaN');

    expect($invitation->fresh()->hasBeenRedeemed())->toBeTrue();
});

it('can create a new using different casing for usernames between the invitation and the registration process', function (): void {
    $invitation = InvitationCode::factory()->create(['username' => 'SaMtHeMaN']);

    expect($invitation->fresh()->hasBeenRedeemed())->toBeFalse();

    session()->flash('username', $invitation->username);

    expect(User::count())->toBe(1);

    /** @var User $user */
    $user = (new CreateNewUser())->create([
        'username'              => 'SAMTHEMAN',
        'code'                  => $invitation->code,
        'password'              => 'SuperStr0ng!',
        'password_confirmation' => 'SuperStr0ng!',
        'terms'                 => true,
    ]);

    $newUser = User::latest('id')->first();

    expect(User::count())->toBe(2);
    expect($newUser->is($user))->toBeTrue();
    expect($newUser->username)->toBe('SAMTHEMAN');

    expect($invitation->fresh()->hasBeenRedeemed())->toBeTrue();
});
