<?php

namespace Conquest\Assemble\Commands;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use function Laravel\Prompts\text;
use Illuminate\Support\Facades\Hash;
use function Laravel\Prompts\multiselect;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Illuminate\Contracts\Console\PromptsForMissingInput;

abstract class UserRoleCommand extends Command implements PromptsForMissingInput
{
    /**
     * The console command name.
     *
     * @var string
     */

    protected $name;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description;

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['email', InputArgument::REQUIRED, 'The email of the user to modify.'],
        ];
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
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array
     */
    protected function promptForMissingArgumentsUsing()
    {
        return [
            'email' => [
                'What is the email of this user?',
                'E.g. john.doe@email.com',
            ],
        ];
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     *
     */
    public function handle()
    {
        if (!($user = $this->getValidatedUser($email = $this->getEmailInput()))) {
            $this->components->error(sprintf('The email [%s] does not match any user.', $email));
            return false;
        }

        try {
            // Update
        } catch (Exception $e) {
            $this->components->error(sprintf('There was an issue updating user [%s].', $email));
            return false;
        }

        $this->components->info(sprintf('User [%s] updated successfully.', $email));
        return true;
    }

    /**
     * Determine if the given email address already exists.
     *
     * @param  string  $email
     * @return Illuminate\Database\Eloquent\Model|null
     *
     */
    protected function getValidatedUser($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        return $this->getUserModel()::where('email', $email)->first();
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