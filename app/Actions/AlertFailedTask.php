<?php

declare(strict_types=1);

namespace App\Actions;

use App\Cache\AlertStore;
use App\DTO\Alert;
use App\Models\Server;
use App\Models\User;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Throwable;

final class AlertFailedTask
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Server $server,
        public string $errorName,
        public Throwable $exception,
        public ?User $user = null
    ) {
    }

    public function handle(): void
    {
        if ($this->user !== null) {
            AlertStore::push($this->user, new Alert($this->errorName, 'warning', $this->server->name));
        }

        report($this->exception);
    }
}
