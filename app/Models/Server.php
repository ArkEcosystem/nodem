<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\HasRoute;
use App\Models\Concerns\HasViewModel;
use App\Models\Concerns\InteractsWithTaskState;
use App\ViewModels\ServerViewModel;
use ARKEcosystem\Foundation\Hermes\Contracts\HasNotificationLogo;
use ARKEcosystem\Foundation\Hermes\Models\Concerns\HasRelatedNotifications;
use ARKEcosystem\Foundation\UserInterface\Eloquent\Concerns\HasSchemalessAttributes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Models\Activity;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes as CastSchemalessAttributes;
use Spatie\SchemalessAttributes\SchemalessAttributes;

/**
 * @property User $user
 * @property SchemalessAttributes $extra_attributes
 * @method ServerViewModel toViewModel()
 */
final class Server extends Model implements HasRoute, HasNotificationLogo
{
    use HasRelatedNotifications;
    use HasViewModel;
    use HasSchemalessAttributes;
    use InteractsWithTaskState;

    protected $casts = [
        'cpu_total'             => 'integer',
        'cpu_used'              => 'float',
        'cpu_available'         => 'float',
        'ram_total'             => 'integer',
        'ram_used'              => 'integer',
        'ram_available'         => 'integer',
        'disk_total'            => 'integer',
        'disk_used'             => 'integer',
        'disk_available'        => 'integer',
        'auth_username'         => 'encrypted',
        'auth_password'         => 'encrypted',
        'auth_access_key'       => 'encrypted',
        'uses_bip38_encryption' => 'boolean',
        'extra_attributes'      => CastSchemalessAttributes::class,
    ];

    protected $with = ['processes'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processes(): HasMany
    {
        return $this->hasMany(Process::class);
    }

    public function resourceIndicators(): HasMany
    {
        return $this->hasMany(ResourceIndicator::class);
    }

    public function usesBasicAuth(): bool
    {
        if (is_null($this->auth_username)) {
            return false;
        }

        if (is_null($this->auth_password)) {
            return false;
        }

        return true;
    }

    public function usesAccessKey(): bool
    {
        if (is_null($this->auth_access_key)) {
            return false;
        }

        return true;
    }

    public function route(): string|null
    {
        return route('server', $this->id);
    }

    public function logo(): Media|null
    {
        return $this->toViewModel()->logo();
    }

    public function fallbackIdentifier(): string|null
    {
        return (string) $this->toViewModel()->id();
    }

    public function coreManagerCurrentVersion(): string
    {
        return (string) $this->getMetaAttribute('core_manager_current_version', '');
    }

    public function coreManagerLatestVersion(): string
    {
        return (string) $this->getMetaAttribute('core_manager_latest_version', '');
    }

    public function hasHeightMismatch(): bool
    {
        return $this->getMetaAttribute('has_height_mismatch') === true;
    }

    public function isUnableToFetchHeight(): bool
    {
        return $this->getMetaAttribute('unable_to_fetch_height') === true;
    }

    public function setSilentUpdate(): void
    {
        $this->fresh()?->setMetaAttribute('silent_update', true);
    }

    public function unsetSilentUpdate(): void
    {
        $this->fresh()?->forgetMetaAttribute('silent_update');
    }

    protected static function booted()
    {
        static::deleting(function (self $server) : void {
            Cache::tags('server-filters:'.$server->id)->flush();
        });

        static::deleted(function (self $server): void {
            Activity::forSubject($server)->delete();
        });
    }
}
