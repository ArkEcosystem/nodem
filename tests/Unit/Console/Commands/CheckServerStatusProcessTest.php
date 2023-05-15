<?php

declare(strict_types=1);

use App\Console\Commands\CheckServerProcessStatus;
use App\Jobs\CheckServerProcessStatus as Job;
use App\Models\Server;
use Illuminate\Support\Facades\Queue;

it('should dispatch a job for each server', function (): void {
    Queue::fake();

    Server::factory(10)->create();

    (new CheckServerProcessStatus())->handle();

    Queue::assertPushed(Job::class, 10);
});
