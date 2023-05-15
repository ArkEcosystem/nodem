<?php

declare(strict_types=1);

use App\Http\Livewire\ActivityLog;
use App\Models\Server;
use App\Models\User;
use Livewire\Livewire;

it('shows the table with the activity log', function (): void {
    $user = User::factory()->create();

    $server = Server::factory()->create();

    $entries = [
        trans('logs.process_started', ['type' => 'Relay']),
        trans('logs.process_stopped', ['type' => 'Relay']),
    ];

    foreach ($entries as $entry) {
        activity()
            ->performedOn($server)
            ->causedBy($user)
            ->withProperties(['username' => $user->username])
            ->log($entry);
    }

    $component = Livewire::actingAs($user)
        ->test(ActivityLog::class, ['server' => $server])
        ->assertDontSee(trans('pages.server.empty_activity_logs', [trans('general.activity')]));

    foreach ($entries as $entry) {
        $component->assertSee($entry);
    }
});

it('doesnt show logs for another server', function (): void {
    $user = User::factory()->create();

    $server = Server::factory()->create();

    $anotherServer = Server::factory()->create();

    $entries = [
        trans('logs.process_started', ['type' => 'Relay']),
        trans('logs.process_stopped', ['type' => 'Relay']),
    ];

    foreach ($entries as $entry) {
        activity()
            ->performedOn($anotherServer)
            ->causedBy($user)
            ->withProperties(['username' => $user->username])
            ->log($entry);
    }

    Livewire::actingAs($user)
        ->test(ActivityLog::class, ['server' => $server])
        ->assertSee(trans('pages.server.empty_activity_logs', [trans('general.activity')]));
});

it('shows no results if no activity log', function (): void {
    $user = User::factory()->create();

    $server = Server::factory()->create();

    Livewire::actingAs($user)
        ->test(ActivityLog::class, ['server' => $server])
        ->assertSee(trans('pages.server.empty_activity_logs', [trans('general.activity')]));
});

it('delete logs on server removal', function (): void {
    $user = User::factory()->create();

    $server = Server::factory()->create();

    $entries = [
        trans('logs.process_started', ['type' => 'Relay']),
        trans('logs.process_stopped', ['type' => 'Relay']),
    ];

    foreach ($entries as $entry) {
        activity()
            ->performedOn($server)
            ->causedBy($user)
            ->withProperties(['username' => $user->username])
            ->log($entry);
    }

    Livewire::actingAs($user)
        ->test(ActivityLog::class, ['server' => $server])
        ->assertDontSee(trans('pages.server.empty_activity_logs', [trans('general.activity')]));

    $server->delete();

    Livewire::actingAs($user)
        ->test(ActivityLog::class, ['server' => $server])
        ->assertSee(trans('pages.server.empty_activity_logs', [trans('general.activity')]));
});
