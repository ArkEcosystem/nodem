<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Server;
use ARKEcosystem\Foundation\Fortify\Components\Concerns\InteractsWithUser;
use Illuminate\Contracts\View\View as Contract;
use Illuminate\Support\Facades\View;
use Livewire\Component;

final class ServerListItem extends Component
{
    use InteractsWithUser;

    public Server $model;

    public string $showAs = 'row';

    public function render(): Contract
    {
        return View::make('livewire.server-list-'.$this->showAs, [
            'server' => $this->model->toViewModel(),
        ]);
    }

    protected function getListeners(): array
    {
        return [
            'serverActionTriggered'.$this->model->id => '$refresh',
            'serverReloadDetails'.$this->model->id   => '$refresh',
        ];
    }
}
