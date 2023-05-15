<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Enums\Constants;
use App\Models\Server;
use App\ViewModels\ViewModelFactory;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasPagination;
use Illuminate\Contracts\View\View as Contract;
use Illuminate\Support\Facades\View;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

final class ActivityLog extends Component
{
    use HasPagination;

    public Server $server;

    public function mount(Server $server) : void
    {
        $this->server = $server;
    }

    public function render(): Contract
    {
        $logs = Activity::forSubject($this->server)->latest()->paginate(Constants::ACTIVITY_LOGS_PER_PAGE);

        return View::make('livewire.activity-log', [
            'logs' => ViewModelFactory::paginate($logs),
        ]);
    }

    protected function getListeners(): array
    {
        return ['serverActionTriggered'.$this->server->id => '$refresh'];
    }
}
