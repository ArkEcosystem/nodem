<?php

declare(strict_types=1);

namespace App\Models;

use App\Http\Livewire\ToggleViewOption;
use App\Models\Concerns\ClearsResponseCache;
use App\Support\TwoFactorAuthenticatable;
use ARKEcosystem\Foundation\Fortify\Models\UserWithoutVerification as Fortify;
use ARKEcosystem\Foundation\Hermes\Models\Concerns\HasNotifications;
use ARKEcosystem\Foundation\UserInterface\Eloquent\Concerns\HasSchemalessAttributes;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Traits\HasRoles;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;

final class User extends Fortify
{
    use ClearsResponseCache;
    use HasSchemalessAttributes;
    use HasNotifications;
    use HasRoles;
    use TwoFactorAuthenticatable;

    protected $casts = [
        'email_verified_at'     => 'datetime',
        'seen_notifications_at' => 'datetime',
        'extra_attributes'      => SchemalessAttributes::class,
    ];

    /** @return HasMany<Server> */
    public function servers(): HasMany
    {
        if ($this->isSuperAdmin()) {
            return $this->hasMany(Server::class);
        }

        return $this->owners->first()?->servers() ?? $this->hasMany(Server::class);
    }

    public function members(): BelongsToMany
    {
        return $this
            ->belongsToMany(self::class, 'teams', 'owner_id', 'member_id')
            ->as('team')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function owners(): BelongsToMany
    {
        return $this
            ->belongsToMany(self::class, 'teams', 'member_id', 'owner_id')
            ->as('team')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function joinAs(string $role, ?self $owner = null): void
    {
        if (! is_null($owner)) {
            $this->owners()->attach($owner->id, ['role' => $role]);
        }

        $this->assignRole($role);
    }

    public function changeRole(string $newRole): void
    {
        $this->roles()->detach();

        $this->assignRole($newRole);
    }

    public function isSuperAdmin(): bool
    {
        if ((bool) $this->owners->count()) {
            return false;
        }

        return true;
    }

    /**
     * Determine if the user has verified their email address.
     *
     * @return bool
     */
    public function hasVerifiedEmail(): bool
    {
        return true;
    }

    /**
     * @codeCoverageIgnore
     *
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification(): void
    {
        //
    }

    /**
     * Get the user's table view option.
     *
     * @return string
     */
    public function defaultTableView() : string
    {
        return $this->getMetaAttribute('table_view_option', ToggleViewOption::DEFAULT_OPTION);
    }

    /**
     * Set the user's default table view in the database.
     *
     * @param string $option
     *
     * @return \App\Models\User
     */
    public function setDefaultTableView(string $option) : self
    {
        return $this->setMetaAttribute('table_view_option', $option);
    }

    public function getHiddenColums(): array
    {
        return Cache::get('hiddenColumns-'.$this->id, []);
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory(): Factory
    {
        return new UserFactory();
    }
}
