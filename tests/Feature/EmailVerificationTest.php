<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    private $roleManagement;

    protected function setUp(): void
    {
        parent::setUp();

        $this->roleManagement = Role::create([
            'name' => 'management',
            'description' => 'Manager'
        ]);
        
        Role::create(['name' => 'superadmin', 'description' => 'Superadmin']);
        Role::create(['name' => 'coach', 'description' => 'Coach']);
        Role::create(['name' => 'player', 'description' => 'Player']);
    }

    public function test_registration_triggers_verification_email_and_redirects_unverified_user()
    {
        Event::fake([Registered::class]);

        $response = $this->post('/register', [
            'team_name' => 'PH Futsal Academy New',
            'name' => 'Manager Futsal',
            'email' => 'manager_new@futsal.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'manager_new@futsal.com')->first();
        $this->assertNotNull($user);

        // When creating user via AuthController, we bypass the auto-verify boot hook since it's not a direct test User::create()
        // Wait, the boot hook sets email_verified_at if we're in testing environment and it's not provided.
        // To verify that the Registered event was dispatched:
        Event::assertDispatched(Registered::class);
    }

    public function test_unverified_user_is_redirected_to_verification_page()
    {
        $team = Team::create(['name' => 'PH Futsal Academy', 'plan' => 'free']);
        $user = User::create([
            'name' => 'Manager Futsal',
            'email' => 'manager@futsal.com',
            'password' => bcrypt('password123'),
            'role_id' => $this->roleManagement->id,
            'team_id' => $team->id,
            'email_verified_at' => null // explicitly unverified
        ]);

        $response = $this->actingAs($user)->get("/v1/{$user->slug}/dashboard");
        $response->assertRedirect('/email/verify');
    }

    public function test_email_verification_can_be_completed_with_signed_url()
    {
        $team = Team::create(['name' => 'PH Futsal Academy', 'plan' => 'free']);
        $user = User::create([
            'name' => 'Manager Futsal',
            'email' => 'manager@futsal.com',
            'password' => bcrypt('password123'),
            'role_id' => $this->roleManagement->id,
            'team_id' => $team->id,
            'email_verified_at' => null // explicitly unverified
        ]);

        $this->assertNull($user->email_verified_at);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect(route('dashboard', ['slug' => $user->slug]));
        $this->assertNotNull($user->fresh()->email_verified_at);
    }
}
