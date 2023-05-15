<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Contracts\ViewModel;
use Carbon\Carbon;

final class ProcessLogViewModel implements ViewModel
{
    private array $log;

    public function __construct(array $log)
    {
        $this->log = $log;
    }

    public function id(): int
    {
        return $this->log['id'];
    }

    public function level(): string
    {
        return $this->log['level'];
    }

    public function date(): string
    {
        return Carbon::createFromTimestamp($this->log['timestamp'])->format('d-m-Y');
    }

    public function time(): string
    {
        return Carbon::createFromTimestamp($this->log['timestamp'])->format('h:i:s');
    }

    public function dateTimeObject(): Carbon
    {
        return Carbon::createFromTimestamp($this->log['timestamp']);
    }

    public function message(): string
    {
        return $this->log['content'];
    }
}
