<?php

namespace App\Traits;

use Illuminate\Support\Str;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\password;

trait GetPasswordTrait
{
    /**
     * @see \App\Console\Commands\CreateUserCommand
     * @see \App\Console\Commands\ChangeUserPasswordCommand
     */
    protected function getPassword(): string
    {
        $password = null;
        $generate = confirm('Do you want to generate password?');

        if ($generate) {
            $password = Str::password(length: 8, symbols: false);

            info("Generated password: {$password}");
        } else {
            $password = password(
                label: 'Set user password',
                required: true,
                validate: ['password' => CreateUserCommand::PASSWORD_VALIDATION_RULES]
            );
        }

        return $password;
    }
}