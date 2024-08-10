<?php

namespace Dyrynda\Artisan\Console\Commands;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use function Laravel\Prompts\text;
use Illuminate\Support\Facades\Password;
use function Laravel\Prompts\multiselect;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user';

    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the user to create.'],
            ['email', InputArgument::REQUIRED, 'The email of the user to create.'],
            ['password', InputArgument::REQUIRED, 'The plaintext password of the user to create.'],
        ];
    }

    protected function getNameInput()
    {
        return trim($this->argument('name'));
    }

    protected function getEmailInput()
    {
        return trim($this->argument('email'));
    }

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
            ['verify', 'v', InputOption::VALUE_NONE, 'Indicate that the email is verified'],
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
        ]))->each(fn ($option) => $option === 'role' ? $role = true : $input->setOption($option, true));
        if ($role) { 
            $role = text('What role value would you like to give this user?');
            $input->setOption('role', $role);
        }
    }


    /**
     * Execute the console command.
     *
     * @return bool|null
     *
     */
    public function handle()
    {
        if (!$this->validateEmail($email = $this->getEmailInput())) {
            $this->components->error(sprintf('The email [%s] is invalid or already exists.', $email));
            return false;
        }

        try {
            
        } catch (Exception $e) {
            $this->components->error(sprintf('There was an issue committing user [%s].', $email));
            return false;
        }

        $this->components->info(sprintf('User [%s] created successfully.', $email));
        return true;

    }

    protected function createUser($name, $email, $password)
    {
        $required = [
            'name' => $name,
            'email' => $email,
            'password' => Password::make($password),
            'email_verified_at' => $this->option('verify') ? now() : null,
            'created_at' => now(),
        ];

        return $this->getUserModel()::create(array_merge(
            $required,
        ));
    }

    /**
     * Determine if the given email address already exists.
     *
     * @param  string  $email
     * @return bool
     *
     */
    protected function validateEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
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
}