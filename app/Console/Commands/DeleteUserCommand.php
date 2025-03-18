<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;

final class DeleteUserCommand extends Command
{
    use \App\Traits\GetUserIdTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:delete
                            {user? : User name, email or ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete user';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $user = $this->argument('user', null);
        $userId = $this->getUserId($user);

        if ($userId === null) {
            error("User '{$user}' can not be found");

            return 1;
        }

        $res = User::find($userId)->delete();

        if ($res !== true) {
            error("User was not deleted");

            return 1;
        }

        info("User was deleted successfully");

        return 0;
    }
}
