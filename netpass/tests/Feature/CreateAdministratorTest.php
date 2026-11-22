<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateAdministratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_creates_an_administrator_with_a_hashed_password(): void
    {
        $this->artisan('admin:create')
            ->expectsQuestion('Administrator name', 'Test Administrator')
            ->expectsQuestion('Email address', 'ADMIN@example.com')
            ->expectsQuestion('Password (at least 12 characters)', 'Test-only-secret-123')
            ->expectsQuestion('Confirm password', 'Test-only-secret-123')
            ->expectsOutput('Administrator created successfully.')
            ->assertSuccessful();
        $user = User::where('email', 'admin@example.com')->firstOrFail();
        $this->assertTrue($user->isAdministrator());
        $this->assertTrue(Hash::check('Test-only-secret-123', $user->password));
        $this->assertNotSame('Test-only-secret-123', $user->password);
    }

    public function test_repeated_setup_does_not_change_an_existing_administrator(): void
    {
        $admin = User::factory()->admin()->create();
        $original = $admin->fresh()->getAttributes();
        $this->artisan('admin:create')
            ->expectsQuestion('Administrator name', 'Replacement')
            ->expectsQuestion('Email address', $admin->email)
            ->expectsOutput('Administrator already exists. No account details or password were changed.')
            ->assertSuccessful();
        $this->assertSame($original, $admin->fresh()->getAttributes());
        $this->assertDatabaseCount('users', 1);
    }

    public function test_command_never_promotes_an_existing_non_admin(): void
    {
        $user = User::factory()->create();
        $this->artisan('admin:create')
            ->expectsQuestion('Administrator name', 'Replacement')
            ->expectsQuestion('Email address', $user->email)
            ->expectsOutput('An account already uses that email. No changes were made.')
            ->assertFailed();
        $this->assertSame('user', $user->fresh()->role);
    }

    public function test_command_rejects_weak_or_mismatched_passwords(): void
    {
        foreach ([['short', 'short'], ['Test-only-secret-123', 'does-not-match'], [str_repeat('a', 73), str_repeat('a', 73)]] as [$password, $confirmation]) {
            $this->artisan('admin:create')
                ->expectsQuestion('Administrator name', 'Test Administrator')
                ->expectsQuestion('Email address', 'admin@example.com')
                ->expectsQuestion('Password (at least 12 characters)', $password)
                ->expectsQuestion('Confirm password', $confirmation)
                ->expectsOutput('Password must have at least 12 characters, fit within 72 bytes, contain no null bytes, and match the confirmation.')
                ->assertFailed();
        }
        $this->assertDatabaseCount('users', 0);
    }

    public function test_command_requires_interactive_input(): void
    {
        $this->artisan('admin:create', ['--no-interaction' => true])->assertFailed();
        $this->assertDatabaseCount('users', 0);
    }
}
