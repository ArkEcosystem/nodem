<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

final class UserObserver
{
    /**
     * Handle the user's `deleting` model event.
     *
     * @param \App\Models\User $user
     *
     * @return void
     */
    public function deleting(User $user): void
    {
        $user->owners()->detach();

        Cache::tags('user-filters:'.$user->id)->flush();
    }
}
