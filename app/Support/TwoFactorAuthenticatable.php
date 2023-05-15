<?php

declare(strict_types=1);

namespace App\Support;

trait TwoFactorAuthenticatable
{
    /**
     * Determine whether the user has enabled 2FA on their account.
     *
     * @return bool
     */
    public function enabledTwoFactor() : bool
    {
        return $this->two_factor_secret !== null;
    }

    /**
     * Determine whether the user has disabled 2FA in the current request.
     *
     * @return bool
     */
    public function recentlyDisabledTwoFactor() : bool
    {
        return $this->two_factor_secret === null
            && $this->getOriginal('two_factor_secret') !== null;
    }
}
