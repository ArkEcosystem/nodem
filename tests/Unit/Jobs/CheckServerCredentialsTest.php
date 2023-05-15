<?php

declare(strict_types=1);

use App\Jobs\CheckServerCredentials;
use App\Models\Server;
use App\Services\Client\Exceptions\RPCResponseException;
use Illuminate\Support\Facades\Http;

it('should thrown an exception if credentials are not correct', function (): void {
    Http::fake(function () {
        throw new RPCResponseException();
    });

    (new CheckServerCredentials(Server::factory()->create()))->handle();
})->throws(RPCResponseException::class);
