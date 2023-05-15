<?php

declare(strict_types=1);

use App\Enums\AlertType;

return [
    AlertType::UPDATING_MANAGER_STATE        => 'An error occurred while updating the manager state. See server logs for details.',
    AlertType::UPDATING_PROCESSES            => 'An error occurred while fetching the server height. See server logs for details.',
    AlertType::UPDATING_SERVER_CORE          => 'An error occurred while fetching the server core version. See server logs for details.',
    AlertType::UPDATING_SERVER_CORE_MANAGER  => 'An error occurred while fetching the server core manager version. See server logs for details.',
    AlertType::UPDATING_SERVER_HEIGHT        => 'An error occurred while fetching the server height. See server logs for details.',
    AlertType::UPDATING_SERVER_PING          => 'An error occurred while trying to ping the server. See server logs for details.',
    AlertType::UPDATING_SERVER_RESOURCES     => 'An error occurred while fetching the server resources. See server logs for details.',
    AlertType::RESTART_CORE_MANAGER          => 'An error occurred while restarting the core manager process. See server logs for details.',
    AlertType::RESTART_SERVER                => 'An error occurred while restarting the server. See server logs for details.',
    AlertType::START_SERVER                  => 'An error occurred while starting the server. See server logs for details.',
    AlertType::STOP_SERVER                   => 'An error occurred while stopping the server. See server logs for details.',
    AlertType::DELETE_PROCESS                => 'An error occurred while deleting the process. See server logs for details.',
    AlertType::OTHER                         => 'An error occurred. See server logs for details.',

    'messages' => [
        'ERR_NO_KEY' => 'The given server has no delegate configured. Configure it first.',
    ],
];
