<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Server;
use App\Models\User;
use App\Services\ServerDetailsUpdaterBatch;
use ARKEcosystem\Foundation\Fortify\Components\Concerns\InteractsWithUser;
use Illuminate\Bus\Batch;
use Illuminate\Bus\BatchRepository;
use Illuminate\Contracts\View\View as Contract;
use Illuminate\Support\Facades\View;
use Livewire\Component;

final class RefreshButton extends Component
{
    use InteractsWithUser;

    public ?Server $server = null;

    public bool $busy = false;

    public array $pendingBatchesIds = [];

    public function render(): Contract
    {
        return View::make('livewire.refresh-button');
    }

    public function refresh(): void
    {
        // Get the batches and change the busy state if all of them are finished.
        $this->busy = ! collect($this->pendingBatchesIds)
            ->map(fn (string $batchId): ?Batch => app(BatchRepository::class)->find($batchId))
            ->filter()
            ->every(fn (Batch $batch): bool => $batch->finished());
    }

    public function update(): void
    {
        /** @var User $user */
        $user = $this->user;

        if ($this->server !== null) {
            $batch = (new ServerDetailsUpdaterBatch($this->server, $user))->dispatch();

            $this->pendingBatchesIds = [$batch->id];

            $this->emit('serverReloadDetails'.$this->server->id);
        } else {
            $this->pendingBatchesIds = $user->servers()->get()->map(
                fn (Server $server) => (new ServerDetailsUpdaterBatch($server, $user))->dispatch()->id
            )->toArray();

            $user->servers()->get()->each(function ($server) {
                $this->emit('serverReloadDetails'.$server->id);
            });

            $this->emit('serverReloadDetails');
        }

        $this->busy = true;
    }
}
