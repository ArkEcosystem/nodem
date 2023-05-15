<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\InvitationCode;
use App\Models\User;
use App\Rules\InvitationCode as InvitationCodeRule;
use App\Rules\Username;
use ARKEcosystem\Foundation\Fortify\Actions\CreateNewUser as FortifyCreateNewUser;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Fortify;

final class CreateNewUser extends FortifyCreateNewUser
{
    public function create(array $input): Authenticatable
    {
        return DB::transaction(function () use ($input): Authenticatable {
            $isNotOwnerUser = User::exists();

            /** @var User $user */
            $user = parent::create($input);

            $invite = tap($this->getInvitationCode($input))
                ->update(['redeemed_at' => now()]);

            // The first user created is the owner and it doesn't have any role.
            // Other users MUST HAVE a role assigned.
            if ($isNotOwnerUser) {
                $user->fresh()?->joinAs($invite->role, $invite->issuer);
            }

            session()->forget(['username']);

            return $user;
        });
    }

    public static function createValidationRules(): array
    {
        return [
            Fortify::username()     => self::usernameRules(),
            'password'              => self::passwordRules(),
            'password_confirmation' => self::passwordConfirmationRules(),
            'code'                  => [sprintf('exclude_if:%s,""', Fortify::username()), 'required', new InvitationCodeRule()],
            'terms'                 => ['required', 'accepted'],
        ];
    }

    public function getUserData(array $input): array
    {
        return [
            Fortify::username() => $input[Fortify::username()],
            'password'          => Hash::make($input['password']),
        ];
    }

    protected static function usernameRules(): array
    {
        return ['required', 'string', 'max:255', 'unique:users', new Username()];
    }

    private function getInvitationCode(array $input): InvitationCode
    {
        return InvitationCode::findByCodeAndUsername($input['code'], $input['username']);
    }
}
