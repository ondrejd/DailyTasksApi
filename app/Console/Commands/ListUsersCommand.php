<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

use function Laravel\Prompts\table;

final class ListUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List existing users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::query()
            ->select(['name', 'email', 'created_at'])
            ->get();

        table(
            ['Name', 'Email', 'Created', 'Tasks'],
            $users->map(fn (User $user) => [
                $user->name,
                $user->email,
                $user->created_at->format('j.n.Y H:i')
            ])
        );
    }
}
