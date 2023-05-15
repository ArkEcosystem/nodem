<?php

declare(strict_types=1);

namespace App\Enums;

final class TeamMemberRole
{
    public const OWNER = 'owner';

    public const ADMIN = 'admin';

    public const MAINTAINER = 'maintainer';

    public const READONLY = 'readonly';
}
