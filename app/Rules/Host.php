<?php

declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;

final class Host implements Rule
{
    public function passes($attribute, $value)
    {
        $parsed = parse_url($value);

        if (! is_array($parsed)) {
            return false;
        }

        if (! Arr::has($parsed, 'host')) {
            return false;
        }

        if (Arr::has($parsed, 'port')) {
            if (filter_var(Arr::get($parsed, 'host'), FILTER_VALIDATE_IP) === false) {
                return false;
            }
        }

        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    public function message()
    {
        return 'The host is invalid. Please https://domain.com or https://ip:port as the host.';
    }
}
