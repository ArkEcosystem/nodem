<?php

declare(strict_types=1);

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

final class PingServer implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public string $host)
    {
        Cache::forget('ping-'.$this->host);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Cache::remember('ping-'.$this->host, now()->addHours(2), function (): bool {
            try {
                $connection = Http::timeout(5)->get($this->host);
            } catch (Exception $exception) {
                return false;
            }

            if ($connection->failed()) {
                return false;
            }

            return true;
        });
    }
}
