<?php

declare(strict_types=1);

namespace App\Enums;

final class ServerUpdatingTasksEnum
{
    public const UPDATING_MANAGER_STATE = 'updating_manager_state';

    public const UPDATING_PROCESSES = 'updating_processes';

    public const UPDATING_SERVER_CORE = 'updating_server_core';

    public const UPDATING_SERVER_CORE_MANAGER = 'updating_server_core_manager';

    public const UPDATING_SERVER_HEIGHT = 'updating_server_height';

    public const UPDATING_SERVER_PING = 'updating_server_ping';

    public const UPDATING_SERVER_RESOURCES = 'updating_server_resources';

    public const SERVER_CORE_MANAGER_RUNNING = 'updating_server_height_manager_running';
}
