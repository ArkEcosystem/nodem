<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Guard;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

final class AccessControlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->resetCache();

        $roles     = $this->getRoles();
        $guardName = Guard::getDefaultName(Permission::class);

        collect($roles)->keys()->each(function ($roleName) use ($roles, $guardName): void {
            /** @var Role $role */
            $role = Role::firstOrCreate(['name' => $roleName]);

            collect($roles[$roleName])->each(fn ($actions, $resource) => collect($actions)->each(fn ($action) => tap(Permission::firstOrCreate([
                'name'       => "{$action} {$resource}",
                'guard_name' => $guardName,
            ]))->assignRole($role)));
        });
    }

    private function resetCache(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    private function getRoles(): array
    {
        return (array) require database_path('seeders/app/roles.php');
    }
}
