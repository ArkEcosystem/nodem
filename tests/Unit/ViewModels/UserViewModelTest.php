<?php

declare(strict_types=1);

use App\Enums\TeamMemberRole;
use App\Models\User;
use App\ViewModels\UserViewModel;
use ARKEcosystem\Foundation\UserInterface\Support\DateFormat;

it('should get the id', function () {
    $subject = new UserViewModel($user = User::factory()->create());

    expect($subject->id())->toBe($user->id);
});

it('should get the username', function () {
    $subject = new UserViewModel($user = User::factory()->create());

    expect($subject->username())->toBe($user->username);
});

it('should get the role', function () {
    $subject = new UserViewModel(User::factory()->create());

    expect($subject->role())->toBe(TeamMemberRole::OWNER);
});

it('should get creation date', function () {
    $subject = new UserViewModel($user = User::factory()->create());

    expect($subject->createdAtLocal())->toBe($user->created_at_local->format(DateFormat::DATE));
});

it('should determine if the user is super admin', function () {
    $subject = new UserViewModel($user = User::factory()->create());

    expect($subject->isSuperAdmin())->toBe($user->isSuperAdmin());
    expect($subject->isSuperAdmin())->toBeBool();
});

it('should get the model', function () {
    $subject = new UserViewModel($user = User::factory()->create());

    expect($subject->model())->toBe($user);
});
