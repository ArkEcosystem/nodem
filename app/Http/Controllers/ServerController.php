<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Server;
use App\ViewModels\ViewModelFactory;
use Illuminate\View\View;

final class ServerController
{
    public function __invoke(Server $server): View
    {
        return view('app.server', [
            'server'      => ViewModelFactory::make($server),
            'serverModel' => $server,
        ]);
    }
}
