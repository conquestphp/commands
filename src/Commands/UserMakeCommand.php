<?php

namespace Conquest\Assemble\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\text;

#[AsCommand(name: 'user:make')]
class UserMakeCommand extends Command implements PromptsForMissingInput
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'user:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the user to create.'],
            ['email', InputArgument::REQUIRED, 'The email of the user to create.'],
            ['password', InputArgument::REQUIRED, 'The plaintext password of the user to create.'],
        ];
    }

    /**
     * Get the desired user name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        return trim($this->argument('name'));
    }

    /**
     * Get the desired user email from the input.
     *
     * @return string
     */
    protected function getEmailInput()
    {
        return trim($this->argument('email'));
    }

    /**
     * Get the desired user password from the input.
     *
     * @return string
     */
    protected function getPasswordInput()
    {
        return trim($this->argument('password'));
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['admin', 'a', InputOption::VALUE_NONE, 'Indicate that the user should be root'],
            ['verify', 'e', InputOption::VALUE_NONE, 'Indicate that the email is verified'],
            ['role', 'r', InputOption::VALUE_OPTIONAL, 'Supply a role according to your definition'],
        ];
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array
     */
    protected function promptForMissingArgumentsUsing()
    {
        return [
            'name' => [
                'What is the name of this user?',
                'E.g. John Doe',
            ],
            'email' => [
                'What is the email of this user?',
                'E.g. john.doe@email.com',
            ],
            'password' => [
                'What is the password of this user?',
                'E.g. password12345',
            ],
        ];
    }

    /**
     * Interact further with the user if they were prompted for missing arguments.
     *
     * @return void
     */
    protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output)
    {
        if ($this->didReceiveOptions($input)) {
            return;
        }
        $role = false;
        collect(multiselect('Would you like any of the following?', [
            'verify' => 'Verify email',
            'admin' => 'Make admin',
            'role' => 'Specify role',
        ]))->each(function ($option) use (&$role, $input) {
            if ($option === 'role') {
                $role = true;
            } else {
                $input->setOption($option, true);
            }
        });

        if ($role) {
            $role = text('What role value would you like to give this user?');
            $input->setOption('role', $role);
        }
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        if (! $this->validateName($name = $this->getNameInput())) {
            $this->components->error(sprintf('The name provided [%s] is invalid.', $name));

            return false;
        }

        if (! $this->validateEmail($email = $this->getEmailInput())) {
            $this->components->error(sprintf('The email [%s] is invalid or already exists.', $email));

            return false;
        }

        if (! $this->validatePassword($password = $this->getPasswordInput())) {
            $this->components->error(sprintf('The password provided [%s] is invalid.', $password));

            return false;
        }

        try {
            $this->createUser($name, $email, $password);
        } catch (Exception $e) {
            $this->components->error(sprintf('There was an issue committing user [%s].', $email));

            return false;
        }

        $this->components->info(sprintf('User [%s] created successfully.', $email));

        return true;
    }

    /**
     * Determine if the name is valid.
     *
     * @param  string  $name
     * @return bool
     */
    protected function validateName($name)
    {
        return true;
    }

    /**
     * Determine if the password is valid.
     *
     * @param  string  $password
     * @return bool
     */
    protected function validatePassword($password)
    {
        return strlen($password) >= 8;
    }

    /**
     * Determine if the given email address already exists.
     *
     * @param  string  $email
     * @return bool
     */
    protected function validateEmail($email)
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return ! $this->getUserModel()::where('email', $email)->exists();
    }

    /**
     * Get the user model.
     *
     * @return string
     */
    protected function getUserModel()
    {
        return Str::replace('::class', '', config('auth.providers.users.model'));
    }

    /**
     * Create a new user.
     *
     * @param  string  $name
     * @param  string  $email
     * @param  string  $password
     * @return Model
     */
    protected function createUser($name, $email, $password)
    {
        $required = [
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => $this->option('verify') ? now() : null,
            'created_at' => now(),
        ];

        $user = ($this->getUserModel())::forceCreate(array_merge(
            $this->option('admin') ? $this->asAdmin() : [],
            $this->option('role') ? $this->asRole() : [],
            $this->extraAttributes(),
            $required,
        ));

        return $user;
    }

    /**
     * Override function allowing for admin options to be set.
     *
     * @return array<string, string>
     */
    protected function asAdmin()
    {
        return [];
    }

    /**
     * Override function allowing for admin options to be set.
     *
     * @return array<string, string>
     */
    protected function asRole()
    {
        return [];
    }

    /**
     * Override function allowing for extra attributes to be set.
     *
     * @return array<string, string>
     */
    protected function extraAttributes()
    {
        return [];
    }
}
