<?php

declare(strict_types=1);

use App\Console\Commands\UpdateServerCoreVersions;
use App\Jobs\UpdateServerCoreVersion as Job;
use App\Models\Server;
use Illuminate\Support\Facades\Queue;

it('should dispatch a job for each server', function (): void {
    Queue::fake();

    Server::factory(10)->create();

    $result = (new UpdateServerCoreVersions())->handle();

    Queue::assertPushed(Job::class, 10);
});
