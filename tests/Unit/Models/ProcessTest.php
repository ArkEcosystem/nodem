<?php

declare(strict_types=1);

use App\Models\Process;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

it('should belong to a server', function (): void {
    $process = Process::factory()->create();

    expect($process->server())->toBeInstanceOf(BelongsTo::class);
});
