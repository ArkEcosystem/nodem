<?php

declare(strict_types=1);

namespace App\DTO;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;

final class Alert
{
    public function __construct(private string $name, private string $type, private string $serverName)
    {
    }

    public function type(): string
    {
        return $this->type;
    }

    public function message(): string
    {
        if (Lang::has('alerts.'.$this->name)) {
            return trans('alerts.'.$this->name);
        }

        /** @var array $messages */
        $messages = Lang::get('alerts.messages', []);

        return Arr::get($messages, $this->name, $this->name);
    }

    public function serverName(): string
    {
        return $this->serverName;
    }
}
