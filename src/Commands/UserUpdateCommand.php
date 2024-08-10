<?php

namespace Conquest\Command\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

abstract class UserUpdateCommand extends Command implements PromptsForMissingInput
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
     */
    public function handle()
    {
        if (! ($user = $this->getValidatedUser($email = $this->getEmailInput()))) {
            $this->components->error(sprintf('The email [%s] does not match any user.', $email));

            return false;
        }

        try {
            $this->updateUser($user);
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
     * @return Model|null
     */
    protected function getValidatedUser($email)
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
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

    /**
     * Update the user.
     *
     * @param  Model  $user
     * @return void
     */
    abstract protected function updateUser($user);
}
