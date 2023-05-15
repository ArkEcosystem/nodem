<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\CreateInvitationCode;
use App\Enums\TeamMemberRole;
use App\Models\InvitationCode;
use App\Models\User;
use ARKEcosystem\Foundation\Fortify\Rules\Username;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

final class UserInvite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:invite
                            {--U|username= : The username that will be linked to a new generated invitation code }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate an invitation code for the first user (Owner)';

    public function handle(): int
    {
        if (User::exists()) {
            $this->alert('Nah, I cannot create another invitation code. Owner account already exists!');

            return 1;
        }

        DB::table((new InvitationCode())->getTable())->truncate();

        do {
            $username = $this->option('username')
                 ?? $this->ask('Which username do you want to use for the owner account?');

            $validator = Validator::make(
                ['username' => $username],
                ['username' => ['required', 'string', resolve(Username::class)]]
            );

            if ($validator->fails()) {
                $this->alert($validator->getMessageBag()->first());
            }
        } while ($validator->fails());

        $code = (new CreateInvitationCode())();

        InvitationCode::create([
            'username' => $username,
            'code'     => $code,
            'role'     => TeamMemberRole::OWNER,
        ]);

        $signUpUrl = config('app.url').'/register';

        $this->warn(<<<TAG
            Here we go!
            You can now open your browser to "{$signUpUrl}" and use this information to register your account:
            TAG);

        $len       = (int) collect([$code, $username])->map(fn ($v) => strlen($v))->sortDesc()->first();
        $separator = str_pad('', $len + 17, '=');
        $code      = str_pad($code, $len, ' ', STR_PAD_LEFT);
        $username  = str_pad($username, $len, ' ', STR_PAD_LEFT);

        $this->info(<<<TAG
            {$separator}
            invitation code: {$code}
            username:        {$username}
            {$separator}
            TAG);

        return 0;
    }
}
