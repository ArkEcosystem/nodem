<?php

declare(strict_types=1);

namespace App\Enums;

final class TeamMemberPermission
{
    public const TEAM_MEMBERS_ADD = 'add team-members';

    public const TEAM_MEMBERS_EDIT = 'edit team-members';

    public const TEAM_MEMBERS_DELETE = 'delete team-members';

    public const TEAM_MEMBERS_INVITE = 'invite team-members';

    public const SERVER_PROCESSES_START = 'start server-processes';

    public const SERVER_PROCESSES_RESTART = 'restart server-processes';

    public const SERVER_PROCESSES_STOP = 'stop server-processes';

    public const SERVER_PROCESSES_DELETE = 'delete server-processes';

    public const SERVER_ADD = 'add server';

    public const SERVER_DELETE = 'delete server';

    public const SERVER_EDIT = 'edit server';

    public const CORE_UPDATE = 'update core';

    public const CORE_MANAGER_UPDATE = 'update core-manager';

    public const SERVER_CONFIGURATION_IMPORT = 'import server-configuration';

    public const SERVER_CONFIGURATION_EXPORT = 'export server-configuration';
}
