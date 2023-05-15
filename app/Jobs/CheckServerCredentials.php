<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Server;
use App\Services\Client\RPC;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

final class CheckServerCredentials implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public Server $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * @throws \App\Services\Client\Exceptions\RPCResponseException | \Illuminate\Http\Client\RequestException | \InvalidArgumentException | \Illuminate\Http\Client\ConnectionException
     */
    public function handle(): void
    {
        Arr::get(RPC::fromServer($this->server)->info()->coreVersion(), 'currentVersion');
    }
}
