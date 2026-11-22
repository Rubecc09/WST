<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_login_page_is_public_and_registration_is_absent(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Welcome back')->assertSee('name="_token"', false);
        $this->get('/register')->assertNotFound();
        $this->post('/register', [])->assertNotFound();
        $this->get('/logout')->assertStatus(405);
    }

    public function test_administrator_login_rotates_session_and_displays_account(): void
    {
        $admin = User::factory()->admin()->create();
        $this->withSession(['marker' => 'preserved']);
        $oldId = session()->getId();
        $this->post(route('login.store'), ['email' => $admin->email, 'password' => 'password', 'remember' => 1])
            ->assertRedirect(route('members.index'));
        $this->assertAuthenticatedAs($admin);
        $this->assertNotSame($oldId, session()->getId());
        $this->assertSame('preserved', session('marker'));
        $this->get(route('members.index'))->assertOk()->assertSee($admin->name)->assertSee($admin->email)->assertSee('Log out');
        $this->get(route('login'))->assertRedirect(route('members.index'));
        $this->assertNotNull($admin->fresh()->remember_token);
    }

    public function test_login_returns_to_an_intended_management_page(): void
    {
        $admin = User::factory()->admin()->create();
        $this->get(route('levels.index'))->assertRedirect(route('login'));
        $this->post(route('login.store'), ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect(route('levels.index'));
    }

    public function test_invalid_unknown_and_non_admin_credentials_have_the_same_error(): void
    {
        $admin = User::factory()->admin()->create();
        $regular = User::factory()->create();
        foreach ([[$admin->email, 'wrong'], ['absent@example.com', 'wrong'], [$regular->email, 'password']] as [$email, $password]) {
            $this->post(route('login.store'), compact('email', 'password'))
                ->assertSessionHasErrors(['email' => 'Unable to sign in with those credentials.'])
                ->assertSessionMissing('_old_input.password');
            $this->assertGuest();
        }
    }

    public function test_login_validates_required_fields_and_email_format(): void
    {
        $this->post(route('login.store'), [])->assertSessionHasErrors(['email', 'password']);
        $this->post(route('login.store'), ['email' => 'invalid', 'password' => 'password'])->assertSessionHasErrors('email');
    }

    public function test_login_is_limited_after_five_failed_attempts_and_recovers(): void
    {
        $admin = User::factory()->admin()->create();
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('login.store'), ['email' => $admin->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        }
        $this->post(route('login.store'), ['email' => $admin->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertStringContainsString('Too many login attempts', session('errors')->first('email'));
        $this->assertGuest();
        $this->travel(61)->seconds();
        $this->post(route('login.store'), ['email' => $admin->email, 'password' => 'password'])->assertRedirect(route('members.index'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_logout_invalidates_session_rotates_token_and_blocks_further_access(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->withSession(['private_marker' => 'remove', '_token' => 'old-token']);
        $oldId = session()->getId();
        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
        $this->assertNotSame($oldId, session()->getId());
        $this->assertNotSame('old-token', session()->token());
        $this->assertNull(session('private_marker'));
        $this->get(route('members.index'))->assertRedirect(route('login'));
    }

    public function test_every_management_route_rejects_guests_and_non_administrators(): void
    {
        $managementRoutes = collect(Route::getRoutes()->getRoutes())->filter(
            fn ($route) => in_array('admin', $route->gatherMiddleware(), true)
        );
        $this->assertCount(31, $managementRoutes);
        foreach ($managementRoutes as $route) {
            $uri = '/'.preg_replace('/\\{[^}]+\\}/', '999', $route->uri());
            foreach (array_diff($route->methods(), ['HEAD']) as $method) {
                $this->call($method, $uri)->assertRedirect(route('login'));
            }
        }
        $this->actingAs(User::factory()->create());
        foreach ($managementRoutes as $route) {
            $uri = '/'.preg_replace('/\\{[^}]+\\}/', '999', $route->uri());
            foreach (array_diff($route->methods(), ['HEAD']) as $method) {
                $this->call($method, $uri)->assertForbidden();
            }
        }
    }

    public function test_administrators_can_access_all_modules_and_role_changes_take_effect(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);
        foreach (['members', 'categories', 'levels'] as $module) {
            $this->get(route($module.'.index'))->assertOk()->assertHeader('Cache-Control', 'no-store, private');
            $this->get(route($module.'.create'))->assertOk();
        }
        $admin->role = 'user';
        $admin->save();
        $this->get(route('members.index'))->assertForbidden();
    }

    public function test_login_and_logout_require_csrf_tokens(): void
    {
        $this->app['env'] = 'production';
        $this->post(route('login.store'), ['email' => 'admin@example.com', 'password' => 'not-a-real-password'])->assertStatus(419);
        $this->actingAs(User::factory()->admin()->create());
        $this->post(route('logout'))->assertStatus(419);
    }

    public function test_new_users_are_not_administrators_by_default(): void
    {
        $user = User::factory()->create();
        $this->assertSame('user', $user->fresh()->role);
        $this->assertFalse($user->fresh()->isAdministrator());
    }

    public function test_ip_rate_limit_also_blocks_attempts_across_different_emails(): void
    {
        for ($attempt = 0; $attempt < 30; $attempt++) {
            $this->post(route('login.store'), [
                'email' => 'attempt'.$attempt.'@example.com',
                'password' => 'wrong',
            ])->assertSessionHasErrors('email');
        }
        $this->post(route('login.store'), [
            'email' => 'another@example.com', 'password' => 'wrong',
        ])->assertStatus(429);
    }
}
