<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Models\PremiumPayment;
use App\Models\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class FreeTrialExpirationTest extends TestCase
{
    use RefreshDatabase;

    private $managementRole;
    private $playerRole;
    private $team;
    private $managerUser;
    private $playerUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed Roles
        $this->managementRole = Role::create(['name' => 'management', 'description' => 'Management']);
        $this->playerRole = Role::create(['name' => 'player', 'description' => 'Player']);

        // Create Team on FREE plan registered today
        $this->team = Team::create([
            'name' => 'FC Trial',
            'plan' => 'free',
            'description' => 'Trial Team'
        ]);
        $this->team->refresh();

        // Create Manager User
        $this->managerUser = User::create([
            'name' => 'Manager Hendra',
            'email' => 'manager@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->managementRole->id,
            'team_id' => $this->team->id,
            'slug' => 'manager-hendra'
        ]);

        // Create Player User
        $this->playerUser = User::create([
            'name' => 'Player Rian',
            'email' => 'player@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->playerRole->id,
            'team_id' => $this->team->id,
            'slug' => 'player-rian'
        ]);
    }

    public function test_active_free_trial_can_access_all_routes_normally()
    {
        $response = $this->actingAs($this->managerUser)
            ->get('/v1/manager-hendra/dashboard');

        $response->assertStatus(200);
        $response->assertSee('FC Trial');
        $response->assertDontSee('Free Trial Expired');

        $response = $this->actingAs($this->managerUser)
            ->get('/v1/manager-hendra/schedules');

        $response->assertStatus(200);
    }

    public function test_expired_free_trial_blocks_restricted_routes_and_redirects_to_dashboard()
    {
        // Manipulate creation date to over 2 months ago (e.g. 3 months ago)
        $this->team->created_at = now()->subMonths(3);
        $this->team->save();

        $response = $this->actingAs($this->managerUser)
            ->get('/v1/manager-hendra/schedules');

        $response->assertRedirect('/v1/manager-hendra/dashboard');
        $response->assertSessionHas('error', 'Masa penggunaan gratis tim Anda telah habis. Silakan hubungi manager Anda atau upgrade ke Premium.');
    }

    public function test_expired_free_trial_sends_email_exactly_once_and_renders_expired_view()
    {
        Mail::fake();

        // Expire the trial
        $this->team->created_at = now()->subMonths(3);
        $this->team->save();

        $this->assertFalse($this->team->free_expiration_email_sent);

        // Visit dashboard
        $response = $this->actingAs($this->managerUser)
            ->get('/v1/manager-hendra/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Free Trial Expired');
        $response->assertSee('Upgrade ke Premium Sekarang');

        // Verify email sent
        Mail::assertSent(\App\Mail\FreeTrialExpiredMail::class, function ($mail) {
            return $mail->user->email === 'manager@test.com' && $mail->team->id === $this->team->id;
        });

        // Verify database flag updated
        $this->team->refresh();
        $this->assertTrue($this->team->free_expiration_email_sent);

        // Reset mail fake status to count again
        Mail::fake();

        // Visit dashboard a second time
        $response2 = $this->actingAs($this->managerUser)
            ->get('/v1/manager-hendra/dashboard');

        $response2->assertStatus(200);

        // Verify email NOT sent again
        Mail::assertNotSent(\App\Mail\FreeTrialExpiredMail::class);
    }

    public function test_manager_can_access_upgrade_and_payment_routes_even_when_expired()
    {
        $this->team->created_at = now()->subMonths(3);
        $this->team->save();

        $response = $this->actingAs($this->managerUser)
            ->get('/v1/manager-hendra/upgrade');

        $response->assertStatus(200);
        $response->assertSee('Pilih Metode Pembayaran');

        // Create pending payment
        $payment = PremiumPayment::create([
            'team_id' => $this->team->id,
            'user_id' => $this->managerUser->id,
            'amount' => 100000.00,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'merchant_ref' => 'TRX-123456',
            'payment_method' => 'QRIS'
        ]);

        $response2 = $this->actingAs($this->managerUser)
            ->get('/v1/manager-hendra/upgrade/payment/TRX-123456');

        $response2->assertStatus(200);
    }

    public function test_player_is_blocked_and_sees_non_manager_warning_when_expired()
    {
        $this->team->created_at = now()->subMonths(3);
        $this->team->save();

        $response = $this->actingAs($this->playerUser)
            ->get('/v1/player-rian/schedules');

        $response->assertRedirect('/v1/player-rian/dashboard');

        $response2 = $this->actingAs($this->playerUser)
            ->get('/v1/player-rian/dashboard');

        $response2->assertStatus(200);
        $response2->assertSee('Silakan hubungi');
        $response2->assertSee('Manager');
        $response2->assertSee('tim Anda');
        $response2->assertDontSee('Upgrade ke Premium Sekarang');
    }
}
