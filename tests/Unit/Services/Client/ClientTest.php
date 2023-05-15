<?php

declare(strict_types=1);

use App\Models\Server;
use App\Services\Client\Client;
use App\Services\Client\Exceptions\RPCResponseException;
use Illuminate\Support\Facades\Http;

it('should create an instance from a server model when it uses basic auth', function (): void {
    $subject = Client::fromServer(Server::factory()->create([
        'auth_username' => 'username',
        'auth_password' => 'password',
    ]));

    expect($subject)->toBeInstanceOf(Client::class);
});

it('should create an instance from a server model when it uses an access key', function (): void {
    $subject = Client::fromServer(Server::factory()->create([
        'auth_username'   => null,
        'auth_password'   => null,
        'auth_access_key' => 'access_key',
    ]));

    expect($subject)->toBeInstanceOf(Client::class);
});

it('should throw an exception if the server has no authentication method', function (): void {
    Client::fromServer(Server::factory()->create([
        'auth_username'   => null,
        'auth_password'   => null,
        'auth_access_key' => null,
    ]));
})->throws(InvalidArgumentException::class);

it('should ping a host and return the delay in milliseconds', function (): void {
    expect(Client::ping('https://127.0.0.1:4040/api'))
        ->toBeInt()
        ->toBeGreaterThanOrEqual(1);
});

it('should ping a host and return false if unreachable', function (): void {
    expect(Client::ping('https://10.10.10.10:4040/api'))
        ->toBeFalse();
});

it('should throw an exception if the server has error', function (): void {
    Http::fake(function () {
        return Http::response([
            'error' => [
                'code'    => -32601,
                'data'    => 'Error: The method does not exist / is not available.',
                'message' => 'The method does not exist / is not available.',
            ],
            'id'      => '16a41dff-ed6d-4f68-82af-3f8482e4e4f0',
            'jsonrpc' => '2.0',
        ]);
    });

    Client::fromServer(Server::factory()->create())->send('info.resource');
})->throws(RPCResponseException::class, 'The method does not exist / is not available.');

it('should throw an exception if the server response is null', function (): void {
    Http::fake(function () {
        return Http::response(null);
    });

    Client::fromServer(Server::factory()->create())->send('info.resource');
})->throws(RPCResponseException::class);

it('should return false when catching a thrown exception', function (): void {
    expect(Client::ping('https://foo.bar.com'))->toBeFalse();
});
