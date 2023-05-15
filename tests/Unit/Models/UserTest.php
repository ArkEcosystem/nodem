<?php

declare(strict_types=1);

use App\Enums\TeamMemberRole;
use App\Models\Server;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Spatie\SchemalessAttributes\SchemalessAttributes;

it('should have many servers', function (): void {
    /** @var User $user */
    $user = User::factory()->create();
    Server::factory(3)->create(['user_id' => $user->id]);

    expect($user->servers())->toBeInstanceOf(HasMany::class);

    Role::create(['name' => TeamMemberRole::ADMIN]);

    $member = tap(User::factory()->create())
        ->joinAs(TeamMemberRole::ADMIN, $user);

    expect($member->servers())->toBeInstanceOf(HasMany::class);
});

it('should accept members to join team with a given role', function (): void {
    Role::create(['name' => TeamMemberRole::ADMIN]);

    /** @var User $owner */
    $owner = User::factory()->create();
    /** @var User $member */
    $member = tap(User::factory()->create())
        ->joinAs(TeamMemberRole::ADMIN, $owner);

    expect($owner->members->first()->id)->toBe($member->id);
    expect($owner->members->first()->team->role)->toBe('admin');
    expect($member->owners->first()->id)->toBe($owner->id);
});

it('should be super admin', function (): void {
    Role::create(['name' => TeamMemberRole::ADMIN]);

    /** @var User $owner */
    $owner = User::factory()->create();
    /** @var User $member */
    $member = tap(User::factory()->create())
        ->joinAs(TeamMemberRole::ADMIN, $owner);

    expect($owner->isSuperAdmin())->toBeTrue();
    expect($member->isSuperAdmin())->toBeFalse();
});

it('can determine whether the user has 2fa enabled', function () {
    expect(User::factory()->create([
        'two_factor_secret' => null,
    ])->enabledTwoFactor())->toBeFalse();

    expect(User::factory()->create([
        'two_factor_secret' => 'some-token',
    ])->enabledTwoFactor())->toBeTrue();
});

it('can determine if user has recently disabled 2fa', function () {
    $user = User::factory()->create([
        'two_factor_secret' => null,
    ])->fill([
        'two_factor_secret' => 'some-token',
    ]);

    expect($user->recentlyDisabledTwoFactor())->toBeFalse();

    $user = User::factory()->create([
        'two_factor_secret' => null,
    ])->fill([
        'two_factor_secret' => null,
    ]);

    expect($user->recentlyDisabledTwoFactor())->toBeFalse();

    $user = User::factory()->create([
        'two_factor_secret' => 'some-token',
    ])->fill([
        'two_factor_secret' => 'some-token',
    ]);

    expect($user->recentlyDisabledTwoFactor())->toBeFalse();

    $user = User::factory()->create([
        'two_factor_secret' => 'some-token',
    ])->fill([
        'two_factor_secret' => null,
    ]);

    expect($user->recentlyDisabledTwoFactor())->toBeTrue();
});

it('has verified email address set to true by default', function (): void {
    $user = User::factory()->create();

    expect($user->hasVerifiedEmail())->toBeTrue();
});

it('can change role', function (): void {
    Role::create(['name' => TeamMemberRole::ADMIN]);
    Role::create(['name' => TeamMemberRole::MAINTAINER]);

    /** @var User $owner */
    $owner = User::factory()->create();
    /** @var User $member */
    $member = tap(User::factory()->create())
        ->joinAs(TeamMemberRole::ADMIN, $owner);

    expect($member->hasRole(TeamMemberRole::ADMIN))->toBeTrue();

    $member->changeRole(TeamMemberRole::MAINTAINER);

    expect($member->hasRole(TeamMemberRole::ADMIN))->toBeFalse();
    expect($member->hasRole(TeamMemberRole::MAINTAINER))->toBeTrue();
});

it('has meta attributes', function () {
    $user = User::factory()->create();

    expect($user->extra_attributes)->toBeInstanceOf(SchemalessAttributes::class);

    $user->setMetaAttribute('foo', 'bar');
    $user->setMetaAttribute('bar', [
        'nested' => 'baz',
    ]);

    expect($user->getMetaAttribute('foo'))->toBe('bar');
    expect($user->getMetaAttribute('unknown'))->toBeNull();
    expect($user->getMetaAttribute('unknown', 'default'))->toBe('default');
    expect($user->getMetaAttribute('bar.nested'))->toBe('baz');
    expect($user->hasMetaAttribute('foo'))->toBeTrue();
    expect($user->hasMetaAttribute('bar.nested'))->toBeTrue();
    expect($user->hasMetaAttribute('bar.nested.invalid'))->toBeFalse();

    $user->forgetMetaAttribute('bar');

    expect($user->getMetaAttribute('bar'))->toBeNull();

    $user->fillMetaAttributes([
        'test'  => 'abc',
        'dummy' => [
            'testing',
        ],
    ]);

    expect($user->getMetaAttribute('test'))->toBe('abc');
    expect($user->getMetaAttribute('dummy'))->toBeArray()->toHaveCount(1);
    expect($user->getMetaAttribute('dummy.0'))->toBe('testing');
    expect($user->getMetaAttribute('dummy.invalid'))->toBeNull();

    expect(User::withExtraAttributes(['test' => 'abc'])->count())->toBe(1);
    expect(User::withExtraAttributes(['test' => 'none'])->count())->toBe(0);
});

it('has a view option', function () : void {
    $user = User::factory()->create();

    // Default...
    expect($user->hasMetaAttribute('table_view_option'))->toBeFalse();
    expect($user->defaultTableView())->toBe('list');

    // Default...
    $user->setMetaAttribute('table_view_option', 'grid')->refresh();
    expect($user->hasMetaAttribute('table_view_option'))->toBeTrue();
    expect($user->defaultTableView())->toBe('grid');

    // Setter...
    $user->setDefaultTableView('list')->refresh();
    expect($user->defaultTableView())->toBe('list');
});

it('it flushes cached filters when deleted', function () {
    $user = User::factory()->create();

    Cache::tags('user-filters:'.$user->id)->put('some-key', 'some-value');

    expect(Cache::tags('user-filters:'.$user->id)->has('some-key'))->toBeTrue();

    $user->delete();

    expect(Cache::tags('user-filters:'.$user->id)->has('some-key'))->toBeFalse();
});

it('can get the hidden columns from the cache', function () {
    $user = User::factory()->create();

    expect($user->getHiddenColums())->toBe([]);

    Cache::set('hiddenColumns-'.$user->id, [
        'usage'    => true,
        'provider' => true,
    ]);

    expect($user->getHiddenColums())->toBe([
        'usage'    => true,
        'provider' => true,
    ]);
});
