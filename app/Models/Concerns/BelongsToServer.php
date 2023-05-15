<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\Server;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToServer
{
    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }
}
