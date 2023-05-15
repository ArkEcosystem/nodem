<?php

declare(strict_types=1);

use App\Http\Livewire\RefreshButton;
use App\Jobs\CheckServerProcessStatus;
use App\Jobs\UpdateProcesses;
use App\Jobs\UpdateServerCoreVersion;
use App\Jobs\UpdateServerHeight;
use App\Jobs\UpdateServerPing;
use App\Jobs\UpdateServerResources;
use App\Models\Server;
use App\Models\User;
use Illuminate\Bus\BatchRepository;
use Illuminate\Support\Facades\Bus;
use Livewire\Livewire;

it('should add all the jobs for updating the server details for all servers', function (): void {
    Bus::fake();

    $user = User::factory()->create();

    Server::factory(2)->create([
        'user_id' => $user->id,
    ]);

    Server::factory()->create();

    Livewire::actingAs($user)
        ->test(RefreshButton::class)->call('update');

    $jobs = [
        CheckServerProcessStatus::class,
        UpdateServerCoreVersion::class,
        UpdateServerHeight::class,
        UpdateServerPing::class,
        UpdateProcesses::class,
        UpdateServerResources::class,
    ];

    Bus::assertBatched(fn () => [$jobs]);

    Bus::assertBatchCount(2);
});

it('should call the job for update all server details for the server used as param', function (): void {
    $user = User::factory()->create();

    Bus::fake();

    Server::factory(2)->create();

    $server = Server::factory()->create();

    Livewire::test(RefreshButton::class, ['server' => $server])->call('update');

    $jobs = [
        CheckServerProcessStatus::class,
        UpdateServerCoreVersion::class,
        UpdateServerHeight::class,
        UpdateServerPing::class,
        UpdateProcesses::class,
        UpdateServerResources::class,
    ];

    Bus::assertBatched(fn () => [$jobs]);

    Bus::assertBatchCount(1);
});

it('updates the busy state according to the state of the batched jobs', function (): void {
    Server::factory(2)->create();

    $server = Server::factory()->create();

    // Used to manually fill the `pendingBatchesIds`
    $testingBatch = Bus::batch([])->dispatch();

    $this->mock(BatchRepository::class)
        ->shouldReceive('find')
        ->andReturn($testingBatch);

    $component = Livewire::test(RefreshButton::class, ['server' => $server])
        ->set('pendingBatchesIds', [$testingBatch->id])
        ->assertSet('busy', false)
        ->call('refresh')
        ->assertSet('busy', true);

    // "Finish" the batch
    $testingBatch->finishedAt = now();

    $component->call('refresh')->assertSet('busy', false);
});
