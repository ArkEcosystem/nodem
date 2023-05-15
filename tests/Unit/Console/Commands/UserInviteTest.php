<?php

declare(strict_types=1);

use App\Models\InvitationCode;
use App\Models\User;

it('asks for username before generating invitation code', function (): void {
    $this->artisan('user:invite')
        ->expectsQuestion('Which username do you want to use for the owner account?', 'johndoe')
        ->expectsOutput(<<<'TAG'
            Here we go!
            You can now open your browser to "http://localhost/register" and use this information to register your account:
            TAG)
        ->assertExitCode(0);

    $this->assertDatabaseHas(InvitationCode::class, ['username' => 'johndoe']);
});

it('generates invitation code for username passed as argument', function (): void {
    $this->artisan('user:invite', ['--username' => 'johndoe'])
        ->assertExitCode(0);

    $this->assertDatabaseHas(InvitationCode::class, ['username' => 'johndoe']);
});

it('fails if username is less than 3 characters', function (): void {
    $this->artisan('user:invite')
        ->expectsQuestion('Which username do you want to use for the owner account?', 'jo')
        ->expectsQuestion('Which username do you want to use for the owner account?', 'john');

    $this->assertDatabaseMissing(InvitationCode::class, ['username' => 'jo']);
});

it('fails if username is more than 30 characters', function (): void {
    $this->artisan('user:invite')
        ->expectsQuestion('Which username do you want to use for the owner account?', 'johndoesuperlongnamethatraiseavalidationerror')
        ->expectsQuestion('Which username do you want to use for the owner account?', 'john');

    $this->assertDatabaseMissing(InvitationCode::class, ['username' => 'johndoesuperlongnamethatraiseavalidationerror']);
});

it('fails if username is not all lowercase', function (): void {
    $this->artisan('user:invite')
        ->expectsQuestion('Which username do you want to use for the owner account?', 'JohnDoe')
        ->expectsQuestion('Which username do you want to use for the owner account?', 'john');

    $this->assertDatabaseMissing(InvitationCode::class, ['username' => 'JohnDoe']);
});

it('fails if username contains forbidden characters', function (): void {
    $this->artisan('user:invite')
        ->expectsQuestion('Which username do you want to use for the owner account?', 'john doe')
        ->expectsQuestion('Which username do you want to use for the owner account?', 'john');

    $this->assertDatabaseMissing(InvitationCode::class, ['username' => 'john doe']);
});

it('fails if username starts with special character', function (): void {
    $this->artisan('user:invite')
        ->expectsQuestion('Which username do you want to use for the owner account?', '_johndoe')
        ->expectsQuestion('Which username do you want to use for the owner account?', 'john');

    $this->assertDatabaseMissing(InvitationCode::class, ['username' => '_johndoe']);
});

it('fails if username ends with special character', function (): void {
    $this->artisan('user:invite')
        ->expectsQuestion('Which username do you want to use for the owner account?', 'johndoe_')
        ->expectsQuestion('Which username do you want to use for the owner account?', 'john');

    $this->assertDatabaseMissing(InvitationCode::class, ['username' => 'johndoe_']);
});

it('fails if username contains consecutive special characters', function (): void {
    $this->artisan('user:invite')
        ->expectsQuestion('Which username do you want to use for the owner account?', 'john__doe')
        ->expectsQuestion('Which username do you want to use for the owner account?', 'john');

    $this->assertDatabaseMissing(InvitationCode::class, ['username' => 'john__doe']);
});

it('fails if user already exists', function (): void {
    User::factory()->create();

    $this->artisan('user:invite')
        ->assertExitCode(1);

    $this->assertDatabaseMissing(InvitationCode::class, ['username' => 'johndoe']);
});
