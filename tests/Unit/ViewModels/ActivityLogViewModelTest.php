<?php

declare(strict_types=1);

use App\Models\Server;
use App\Models\User;
use App\ViewModels\ActivityLogViewModel;

beforeEach(function () {
    $this->user = User::factory()->create([
        'username' => 'alfy',
    ]);

    $this->server = Server::factory()->create();

    $this->log = activity()
        ->performedOn($this->server)
        ->causedBy($this->user)
        ->withProperties(['username' => $this->user->username])
        ->log('The log');

    $this->log->created_at = '2020-11-10 09:08:07';
    $this->log->save();

    $this->subject = new ActivityLogViewModel($this->log);
});

it('should get the id', function (): void {
    expect($this->subject->id())->toBe($this->log->id);
});

it('should get the user name', function (): void {
    expect($this->subject->userName())->toBe('alfy');
});

it('should get the username from the property', function (): void {
    expect($this->log->causer)->toBe($this->user);

    $this->user->delete();

    $this->log->refresh();

    expect($this->log->causer)->toBeNull();

    expect($this->subject->userName())->toBe('alfy');
});

it('should get the date formatted', function (): void {
    expect($this->subject->date())->toBe('10-11-2020');
});

it('should get the time formatted', function (): void {
    expect($this->subject->time())->toBe('09:08:07');
});

it('should get the description', function (): void {
    expect($this->subject->description())->toBe('The log');
});
