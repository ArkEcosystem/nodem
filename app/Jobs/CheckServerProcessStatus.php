<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Server;
use App\Services\Client\RPC;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

final class CheckServerProcessStatus implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use Batchable;

    public int $timeout = 10;

    public Server $server;

    public int $threshold;

    public function __construct(Server $server)
    {
        $this->server = $server;

        $this->setThresholdByProcessType();
    }

    public function handle(): void
    {
        $result = RPC::fromServer($this->server)->info()->blockchainHeight();

        $height       = (int) Arr::get($result, 'height');
        $randomHeight = (int) Arr::get($result, 'randomNodeHeight');

        $this->server->setMetaAttribute('has_height_mismatch', abs($height - $randomHeight) >= $this->threshold);
    }

    public function threshold(int $threshold): self
    {
        $this->threshold = $threshold;

        return $this;
    }

    private function setThresholdByProcessType(): void
    {
        $processes = $this->server->processes;

        if ($processes->count() === 1 && Arr::get($processes, '0.type') === 'relay') {
            $this->threshold((int) config('nodem.height_mismatch.threshold.relay'));

            return;
        }

        $this->threshold((int) config('nodem.height_mismatch.threshold.default'));
    }
}
