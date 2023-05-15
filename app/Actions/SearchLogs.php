<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Server;
use App\Services\Client\RPC;
use Carbon\Carbon;

final class SearchLogs
{
    private Server $server;

    private array $parameters = ['limit' => 100];

    private function __construct(Server $server)
    {
        $this->server = $server;
    }

    public static function new(Server $server): self
    {
        return new static($server);
    }

    public function limit(int $limit) : self
    {
        $this->parameters['limit'] = $limit;

        return $this;
    }

    public function process(string $process): self
    {
        if ($process === 'core') {
            $this->parameters['processes'] = ['relay', 'forger'];
        } else {
            $this->parameters['processes'] = [$process];
        }

        return $this;
    }

    public function time(Carbon $start, Carbon $end): self
    {
        $this->parameters['dateFrom'] = $start->unix();
        $this->parameters['dateTo']   = $end->unix();

        return $this;
    }

    public function timeFrom(Carbon $date): self
    {
        $this->parameters['dateFrom'] = $date->unix();

        return $this;
    }

    public function timeTo(Carbon $date): self
    {
        $this->parameters['dateTo'] = $date->unix();

        return $this;
    }

    public function level(string $level): self
    {
        $this->parameters['level'] = $level;

        return $this;
    }

    public function levels(array $levels): self
    {
        $this->parameters['levels'] = $levels;

        return $this;
    }

    public function page(int $page): self
    {
        $this->parameters['offset'] = $page * 100;

        return $this;
    }

    public function term(string $searchTerm): self
    {
        if (strlen($searchTerm) >= 3) {
            $this->parameters['searchTerm'] = $searchTerm;
        }

        return $this;
    }

    public function search(): array
    {
        return RPC::fromServer($this->server)
            ->log()
            ->search(array_filter($this->parameters));
    }

    public function download() : array
    {
        return RPC::fromServer($this->server)
                    ->log()
                    ->download(array_filter($this->parameters));
    }
}
