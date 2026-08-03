<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeAdmin extends Command
{
    protected $signature = 'admin:make {email : The email address of the user to promote}';

    protected $description = 'Promote a registered user to admin by their email address';

    public function handle(): int
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("No user found with email: {$email}");

            return self::FAILURE;
        }

        $user->update(['is_admin' => true]);

        $this->info("User [{$user->name}] ({$user->email}) is now an admin.");

        return self::SUCCESS;
    }
}
