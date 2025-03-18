<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;

final class ChangeUserPasswordCommand extends Command
{
    use \App\Traits\GetPasswordTrait;
    use \App\Traits\GetUserIdTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:password-change
                            {user? : User name, email or ID}
                            {password? : New user password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $user = $this->argument('user', null);
        $password = $this->argument('password', null);
        $userId = $this->getUserId($user);

        if ($userId === null) {
            error("User '{$user}' can not be found");

            return 1;
        }

        if ($password === null) {
            $password = $this->getPassword();
        }

        $user = User::find($userId);

        if ($user === null) {
            error('User was not found');

            return 1;
        }

        $user->password = Hash::make($password);

        info('Password was successfully changed');

        return 0;
    }
}
