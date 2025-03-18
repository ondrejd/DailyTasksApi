<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use function Laravel\Prompts\info;
use function Laravel\Prompts\text;

final class CreateUserCommand extends Command implements PromptsForMissingInput
{
    use \App\Traits\GetPasswordTrait;

    // These constants are not used just in this class...
    public const NAME_VALIDATION_RULES = 'required|max:255|unique:users,name';
    public const EMAIL_VALIDATION_RULES = 'required|email|unique:users,email';
    public const PASSWORD_VALIDATION_RULES = 'required|string|min:8';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:create 
                            {name : User name} 
                            {email : E-mail address} 
                            {password? : User password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new user';

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array<string, string>
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'name' => fn() => text(
                label: 'Set user name',
                placeholder: 'E.g. John Doe',
                required: true,
                validate: ['name' => self::NAME_VALIDATION_RULES]
            ),
            'email' => fn() => text(
                label: 'Set user email',
                placeholder: 'E.g. john.doe@email.com',
                required: true,
                validate: ['email' => self::EMAIL_VALIDATION_RULES]
            ),
        ];
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password', null);

        if ($password === null) {
            $password = $this->getPassword();
        }

        // We do validation again because if values are passed directly 
        // as arguments there is no validation performed yet.
        Validator::validate(
            [
                'name' => $name,
                'email' => $email,
                'password' => $password
            ],
            [
                'name' => self::NAME_VALIDATION_RULES,
                'email' => self::EMAIL_VALIDATION_RULES,
                'password' => self::PASSWORD_VALIDATION_RULES,
            ],
        );

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        info("User successfully created with ID {$user->id}.");
    }
}
