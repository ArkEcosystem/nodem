<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToServer;

final class ResourceIndicator extends Model
{
    use BelongsToServer;

    protected $casts = [
        'cpu'  => 'float',
        'ram'  => 'integer',
        'disk' => 'integer',
    ];
}
