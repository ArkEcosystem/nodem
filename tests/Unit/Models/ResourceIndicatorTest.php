<?php

declare(strict_types=1);

use App\Models\ResourceIndicator;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

it('should belong to a server', function (): void {
    $indicator = ResourceIndicator::factory()->create();

    expect($indicator->server())->toBeInstanceOf(BelongsTo::class);
});
