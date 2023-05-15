<?php

declare(strict_types=1);

namespace App\Rules;

use ARKEcosystem\Foundation\Fortify\Rules\Username as FortifyUsername;

final class Username extends FortifyUsername
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Handle potential NULL values
        $value = $value ?? '';

        if ($this->withForbiddenSpecialChars($value)) {
            $this->withForbiddenSpecialChars = true;

            return false;
        }

        if ($this->withSpecialCharAtTheStart($value)) {
            $this->withSpecialCharAtTheStart = true;

            return false;
        }

        if ($this->withSpecialCharAtTheEnd($value)) {
            $this->withSpecialCharAtTheEnd = true;

            return false;
        }

        if ($this->withConsecutiveSpecialChars($value)) {
            $this->withConsecutiveSpecialChars = true;

            return false;
        }

        if ($this->needsMaximumLength($value)) {
            $this->hasReachedMaxLength = true;

            return false;
        }

        if ($this->withReservedName($attribute, $value)) {
            $this->withReservedName = true;

            return false;
        }

        return ! $this->needsMinimumLength($value);
    }
}
