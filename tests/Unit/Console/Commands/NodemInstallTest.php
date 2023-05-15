<?php

declare(strict_types=1);

use App\Models\InvitationCode;
use App\Models\User;

it('prompts if you are ready to start and we answer no', function (): void {
    $this->artisan('nodem:install')
        ->expectsQuestion('Are you ready to start?', false)
        ->assertExitCode(2);
});

it('prompts if you are ready to start and we answer yes', function (): void {
    $this->artisan('nodem:install')
        ->expectsQuestion('Are you ready to start?', true)
        ->expectsQuestion('Which username do you want to use for the owner account?', 'johndoe')
        ->assertExitCode(0);

    $this->assertDatabaseHas(InvitationCode::class, ['username' => 'johndoe']);
});

it('prompts if you are ready to start and we answer yes and passing a username', function (): void {
    $this->artisan('nodem:install --username=johndoe')
        ->expectsQuestion('Are you ready to start?', true)
        ->assertExitCode(0);

    $this->assertDatabaseHas(InvitationCode::class, ['username' => 'johndoe']);
});

it('fails if user already exists', function (): void {
    User::factory()->create();

    $this->artisan('nodem:install --username=johndoe')
        ->assertExitCode(1);

    $this->assertDatabaseMissing(InvitationCode::class, ['username' => 'johndoe']);
});
