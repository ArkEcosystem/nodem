<?php

declare(strict_types=1);

namespace App\Models;

use App\Cache\ServerStore;
use App\Enums\ServerTypeEnum;
use App\Models\Concerns\BelongsToServer;
use App\Models\Concerns\HasViewModel;
use App\Services\Client\RPC;
use Exception;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property Server $server
 */
final class Process extends Model
{
    use HasViewModel;
    use BelongsToServer;

    protected $casts = [
        'pid' => 'integer',
        'cpu' => 'float',
        'ram' => 'float',
    ];

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->whereIn('type', $type === 'all' ? ServerTypeEnum::toArray() : [$type]);
    }

    public function restart(): array
    {
        return RPC::fromServer($this->server)
            ->process()
            ->restart($this->name);
    }

    public function start(array $options = []): array
    {
        return RPC::fromServer($this->server)
            ->process()
            ->start($this->type, $options);
    }

    public function stop(): array
    {
        return RPC::fromServer($this->server)
            ->process()
            ->stop($this->name);
    }

    public function remove(): array
    {
        return RPC::fromServer($this->server)
            ->process()
            ->delete($this->name);
    }

    public function fetchStatus(): string|null
    {
        try {
            $response = RPC::fromServer($this->server)->process()->list();
            $process  = collect($response)->firstWhere('name', $this->name);

            return $process['status'];
        } catch (Exception $e) {
            report($e);

            return null;
        }
    }

    public function markAs(string $status): void
    {
        $this->fill([
            'status' => $status,
        ])->save();

        ServerStore::flush($this->server);
    }
}
