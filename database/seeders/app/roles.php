<?php

declare(strict_types=1);

use App\Enums\TeamMemberRole;

return [
    TeamMemberRole::ADMIN => [
        'team-members'         => ['add', 'edit', 'delete', 'invite'],
        'server-processes'     => ['start', 'restart', 'stop', 'delete'],
        'server'               => ['add', 'delete', 'edit'],
        'core'                 => ['update'],
        'core-manager'         => ['update'],
        'server-configuration' => ['export', 'import'],
    ],
    TeamMemberRole::MAINTAINER => [
        'server-processes' => ['start', 'restart', 'stop'],
        'core'             => ['update'],
        'core-manager'     => ['update'],
    ],
    TeamMemberRole::READONLY => [],
];
