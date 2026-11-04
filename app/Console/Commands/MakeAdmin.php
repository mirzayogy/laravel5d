<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeAdmin extends Command
{
    protected $signature = 'habitude:make-admin {email : Email of an existing user} {--revoke : Remove the admin role instead}';

    protected $description = 'Give a user the admin role (or take it away with --revoke)';

    public function handle(): int
    {
        $user = User::firstWhere('email', $this->argument('email'));

        if (! $user) {
            $this->error('No user with that email.');

            return self::FAILURE;
        }

        $user->forceFill(['role' => $this->option('revoke') ? User::ROLE_USER : User::ROLE_ADMIN])->save();
        $this->info("{$user->email} is now {$user->role}.");

        return self::SUCCESS;
    }
}
