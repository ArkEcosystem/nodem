<?php

declare(strict_types=1);

namespace App\Services\Client;

use App\Enums\Constants;
use App\Models\Server;
use App\Services\Client\Exceptions\RPCResponseException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

final class Client
{
    private string $host;

    private PendingRequest $client;

    private int $timeout = Constants::CLIENT_TIMEOUT;

    private array $options = [];

    private function __construct(string $host, PendingRequest $client)
    {
        $this->host   = $host;
        $this->client = $client;
    }

    public static function fromServer(Server $server): self
    {
        if ($server->usesBasicAuth()) {
            $client = Http::withBasicAuth($server->auth_username, $server->auth_password);
        } elseif ($server->usesAccessKey()) {
            $client = Http::withToken($server->auth_access_key);
        } else {
            throw new InvalidArgumentException('The given server has no authentication method.');
        }

        return new static($server->host, $client);
    }

    /**
     * Initiate a raw HTTP GET request to the server endpoint.
     *
     * @param string $path
     *
     * @return \Illuminate\Http\Client\Response
     */
    public function get(string $path) : Response
    {
        return $this->client
            ->withOptions($this->options)
            ->get($path)
            ->throw();
    }

    public function withTimeout(int $timeout): self
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function withOptions(array $options = []): self
    {
        $this->options = $options;

        return $this;
    }

    public function send(string $method, array $params = []): array
    {
        return $this->sendRequest($method, $params)['result'];
    }

    public function sendRaw(string $method, array $params = []): string
    {
        return $this->sendRequest($method, $params)['result'];
    }

    public static function ping(string $url, int $timeout = 5) : int | bool
    {
        // The url contains a scheme and host, but we need the host only.
        $host = parse_url($url, PHP_URL_HOST);

        $command = sprintf('ping -W %d -c 1 %s', $timeout, $host);

        exec($command, $output, $exitcode);

        // Exitcode 0 means the host is reachable.
        if ($exitcode !== 0) {
            return false;
        }

        return static::extractTimeFromPingResponse($output);
    }

    private function sendRequest(string $method, array $params = []): array
    {
        $response = $this->client
            ->timeout($this->timeout)
            ->withHeaders(['Content-Type' => 'application/vnd.api+json'])
            ->withOptions($this->options)
            ->post($this->host, [
                'jsonrpc' => '2.0',
                'id'      => (string) Uuid::uuid4(),
                'method'  => $method,
                'params'  => (object) $params,
            ])
            ->throw()
            ->json();

        $this->timeout = Constants::CLIENT_TIMEOUT;

        if (is_null($response) || collect($response)->keys()->containsStrict('error')) {
            throw new RPCResponseException(
                message: (string) data_get($response, 'error.message', ''),
                code: (int) data_get($response, 'error.code', 0)
            );
        }

        return $response;
    }

    private static function extractTimeFromPingResponse(array $output): int
    {
        $outputText = implode('', $output);
        $parts      = explode('=', $outputText);
        $data       = explode('/', end($parts));

        $avgPing = Arr::get($data, 1, false);

        return (int) ceil((float) $avgPing);
    }
}
