<?php

declare(strict_types=1);

use App\Models\Server;
use App\Services\ServerDetailsUpdaterBatch;
use Illuminate\Support\Facades\Bus;

it('sets silent update to the server', function (): void {
    Bus::fake();

    $server  = Server::factory()->create();
    $subject = new ServerDetailsUpdaterBatch($server);

    $batch = $subject->dispatchSilently();

    expect($server->fresh()->getMetaAttribute('silent_update'))->toBeTrue();

    // Call the finally callback
    $batch->options['finally'][0]();

    expect($server->fresh()->getMetaAttribute('silent_update'))->toBeNull();
});

it('should initially dispatch 1 job', function (): void {
    Bus::fake();

    $server  = Server::factory()->create();
    $subject = new ServerDetailsUpdaterBatch($server);

    $batch = $subject->dispatch();

    Bus::assertBatched(function ($batch) {
        return $batch->jobs->count() === 1;
    });
});

it('should dispatch additional jobs on success', function (): void {
    Bus::fake();

    $server  = Server::factory()->create();
    $subject = new ServerDetailsUpdaterBatch($server);

    $batch = $subject->dispatch();

    Bus::assertBatched(function ($batch) {
        return $batch->jobs->count() === 1;
    });

    expect($batch->processedJobs())->toBe(0);
    expect($batch->totalJobs)->toBe(1);

    Bus::fake();

    $batch->options['then'][0]();

    Bus::assertBatched(function ($batch) {
        return $batch->jobs->count() === 5;
    });
});
