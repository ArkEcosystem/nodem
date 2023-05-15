<?php

declare(strict_types=1);

namespace App\Enums;

final class AlertType
{
    public const OTHER = 'other';

    public const UPDATING_MANAGER_STATE = 'updating_manager_state';

    public const UPDATING_PROCESSES = 'updating_processes';

    public const UPDATING_SERVER_CORE = 'updating_server_core';

    public const UPDATING_SERVER_CORE_MANAGER = 'updating_server_core_manager';

    public const UPDATING_SERVER_HEIGHT = 'updating_server_height';

    public const UPDATING_SERVER_PING = 'updating_server_ping';

    public const UPDATING_SERVER_RESOURCES = 'updating_server_resources';

    public const RESTART_CORE_MANAGER = 'restart_core_manager';

    public const RESTART_SERVER = 'restart_server';

    public const START_SERVER = 'start_server';

    public const STOP_SERVER = 'stop_server';

    public const DELETE_PROCESS = 'delete_process';

    public static function toArray(): array
    {
        return [
            static::UPDATING_MANAGER_STATE,
            static::UPDATING_PROCESSES,
            static::UPDATING_SERVER_CORE,
            static::UPDATING_SERVER_CORE_MANAGER,
            static::UPDATING_SERVER_HEIGHT,
            static::UPDATING_SERVER_PING,
            static::UPDATING_SERVER_RESOURCES,
            static::RESTART_CORE_MANAGER,
            static::RESTART_SERVER,
            static::START_SERVER,
            static::STOP_SERVER,
            static::DELETE_PROCESS,
            static::OTHER,
        ];
    }
}
