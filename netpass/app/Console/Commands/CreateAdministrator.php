<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateAdministrator extends Command
{
    protected $signature = 'admin:create';

    protected $description = 'Create an administrator using hidden password prompts';

    public function handle(): int
    {
        if (! $this->input->isInteractive()) {
            $this->error('Run php artisan admin:create interactively.');

            return self::FAILURE;
        }

        $name = trim((string) $this->ask('Administrator name'));
        $email = mb_strtolower(trim((string) $this->ask('Email address')));
        $validator = Validator::make(compact('name', 'email'), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ]);
        if ($validator->fails()) {
            $this->error($validator->errors()->first());

            return self::FAILURE;
        }
        $existing = User::whereRaw('LOWER(email) = ?', [$email])->first();
        if ($existing) {
            if ($existing->isAdministrator()) {
                $this->info('Administrator already exists. No account details or password were changed.');

                return self::SUCCESS;
            }
            $this->error('An account already uses that email. No changes were made.');

            return self::FAILURE;
        }

        $password = $this->secret('Password (at least 12 characters)', false);
        $confirmation = $this->secret('Confirm password', false);
        $validator = Validator::make([
            'password' => $password,
            'password_confirmation' => $confirmation,
        ], ['password' => ['required', 'string', Password::min(12), 'confirmed']]);
        if ($validator->fails() || strlen((string) $password) > 72 || str_contains((string) $password, "\0")) {
            $this->error('Password must have at least 12 characters, fit within 72 bytes, contain no null bytes, and match the confirmation.');

            return self::FAILURE;
        }

        $user = new User;
        $user->name = $name;
        $user->email = $email;
        $user->role = 'admin';
        $user->password = Hash::make($password);
        try {
            $user->save();
        } catch (UniqueConstraintViolationException $exception) {
            $this->error('An account already uses that email. No changes were made.');

            return self::FAILURE;
        }
        $this->info('Administrator created successfully.');

        return self::SUCCESS;
    }
}
