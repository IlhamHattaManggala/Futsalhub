<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    private $managementRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles
        $this->managementRole = Role::create([
            'name' => 'management',
            'description' => 'Management'
        ]);
    }

    public function test_google_redirect_returns_redirect_response()
    {
        $response = $this->get(route('auth.google.redirect'));
        
        $response->assertStatus(302);
        $this->assertStringContainsString('accounts.google.com', $response->getTargetUrl());
    }

    public function test_google_callback_logs_in_existing_user()
    {
        // Create an existing team and user
        $team = Team::create([
            'name' => 'Existing Futsal Team',
            'plan' => 'free',
        ]);

        $user = User::create([
            'name' => 'Google User',
            'email' => 'googleuser@test.com',
            'google_id' => '123456789',
            'role_id' => $this->managementRole->id,
            'team_id' => $team->id,
            'password' => null
        ]);

        // Mock Socialite
        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->shouldReceive('getId')->andReturn('123456789')
            ->shouldReceive('getName')->andReturn('Google User')
            ->shouldReceive('getEmail')->andReturn('googleuser@test.com')
            ->shouldReceive('getAvatar')->andReturn('https://google.com/avatar.jpg');

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/auth/google/callback');

        $response->assertStatus(302);
        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::id());
    }

    public function test_google_callback_redirects_new_user_to_complete_registration()
    {
        // Mock Socialite
        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->shouldReceive('getId')->andReturn('987654321')
            ->shouldReceive('getName')->andReturn('New Google User')
            ->shouldReceive('getEmail')->andReturn('newgoogleuser@test.com')
            ->shouldReceive('getAvatar')->andReturn('https://google.com/new-avatar.jpg');

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('register.google.complete'));
        
        // Assert session has google user details
        $this->assertEquals('987654321', session('google_user.id'));
        $this->assertEquals('New Google User', session('google_user.name'));
        $this->assertEquals('newgoogleuser@test.com', session('google_user.email'));
    }

    public function test_new_user_can_complete_google_registration_with_team_name()
    {
        // Put google user data into session
        $this->withSession([
            'google_user' => [
                'id' => '987654321',
                'name' => 'New Google User',
                'email' => 'newgoogleuser@test.com',
                'avatar' => 'https://google.com/new-avatar.jpg'
            ]
        ]);

        $response = $this->post(route('register.google.complete.post'), [
            'team_name' => 'New Futsal Club'
        ]);

        // Should redirect to dashboard
        $response->assertStatus(302);
        
        // Assert team and user are created
        $this->assertDatabaseHas('teams', [
            'name' => 'New Futsal Club',
            'plan' => 'free'
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'New Google User',
            'email' => 'newgoogleuser@test.com',
            'google_id' => '987654321',
            'avatar' => 'https://google.com/new-avatar.jpg'
        ]);

        $this->assertTrue(Auth::check());
        $this->assertNull(session('google_user')); // Session is cleared
    }
}
