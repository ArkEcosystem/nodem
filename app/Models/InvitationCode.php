<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property Carbon $created_at
 */
final class InvitationCode extends Model
{
    use HasFactory;

    protected $casts = [
        'redeemed_at' => 'datetime',
    ];

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUsername(Builder $query, string $username): Builder
    {
        return $query->whereRaw('lower(username) = ?', Str::lower($username));
    }

    public function scopeHasPending(Builder $query): bool
    {
        return $query->where('redeemed_at', null)->count() > 0;
    }

    public function hasBeenRedeemed(): bool
    {
        return $this->redeemed_at !== null;
    }

    public static function userHasBeenInvited(string $username): bool
    {
        return self::username($username)->whereNull('redeemed_at')->exists();
    }

    public static function userIsATeamMember(string $username): bool
    {
        return self::username($username)->whereNotNull('redeemed_at')->exists();
    }

    public static function findByCodeAndUsername(string $code, string $username): self
    {
        return self::username($username)->where('code', $code)->firstOrFail();
    }
}
