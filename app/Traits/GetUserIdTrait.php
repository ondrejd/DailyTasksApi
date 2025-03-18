<?php

namespace App\Traits;

use App\Models\User;
use function Laravel\Prompts\search;

trait GetUserIdTrait
{
    /**
     * @see \App\Console\Commands\DeleteUserCommand
     * @see \App\Console\Commands\ChangeUserPasswordCommand
     */
    protected function getUserId(int|string|null $user): int|null
    {
        // We already have it
        if (is_numeric($user)) {
            return (int) $user;
        }
        
        // Search for user
        if ($user === null) {
            return search(
                label: 'Search for the user by name or email',
                options: fn (string $value) => strlen($value) > 0
                    ? User::query()
                        ->whereLike('name', "%{$value}%")
                        ->orWhereLike('email', "%{$value}%")
                        ->pluck('name', 'id')
                        ->all()
                    : []
            );
        }

        // Try find corresponding model by name or email
        return User::query()
            ->whereLike('name', "%{$user}%")
            ->orWhereLike('email', "%{$user}%")
            ->pluck('id')
            ->first();
    }
}