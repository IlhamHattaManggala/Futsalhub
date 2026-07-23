<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Models\Player;
use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\Finance;
use App\Models\Statistic;
use App\Models\MatchGame;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;


class FutsalBoardTest extends TestCase
{
    use RefreshDatabase;

    private $superadminRole;
    private $managementRole;
    private $coachRole;
    private $playerRole;
    private $team;
    private $managerUser;
    private $coachUser;
    private $playerUser;
    private $playerProfile;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed Roles
        $this->superadminRole = Role::create(['name' => 'superadmin', 'description' => 'Superadmin']);
        $this->managementRole = Role::create(['name' => 'management', 'description' => 'Management']);
        $this->coachRole = Role::create(['name' => 'coach', 'description' => 'Coach']);
        $this->playerRole = Role::create(['name' => 'player', 'description' => 'Player']);

        // Create Team
        $this->team = Team::create([
            'name' => 'FC Antigravity',
            'plan' => 'premium',
            'description' => 'Test Team'
        ]);

        // Create Manager User
        $this->managerUser = User::create([
            'name' => 'Manager Hendra',
            'email' => 'manager@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->managementRole->id,
            'team_id' => $this->team->id,
            'slug' => 'manager-hendra'
        ]);

        // Create Coach User
        $this->coachUser = User::create([
            'name' => 'Coach Ilham',
            'email' => 'coach@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->coachRole->id,
            'team_id' => $this->team->id,
            'slug' => 'coach-ilham'
        ]);

        // Create Player User and Profile
        $this->playerUser = User::create([
            'name' => 'Rian Pemain',
            'email' => 'player@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->playerRole->id,
            'team_id' => $this->team->id,
            'slug' => 'rian-pemain'
        ]);

        $this->playerProfile = Player::create([
            'team_id' => $this->team->id,
            'user_id' => $this->playerUser->id,
            'name' => 'Rian Pemain',
            'number' => 7,
            'position' => 'Flank'
        ]);
    }

    public function test_unauthenticated_users_are_redirected_to_login()
    {
        $response = $this->get('/v1/rian-pemain/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_manager_can_access_dashboard()
    {
        $response = $this->actingAs($this->managerUser)
            ->get('/v1/manager-hendra/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Manager Hendra');
        $response->assertSee('FC Antigravity');
    }

    public function test_player_can_access_their_profile_and_stats()
    {
        // Add match and statistic
        $match = MatchGame::create([
            'team_id' => $this->team->id,
            'opponent' => 'Lawan FC',
            'date' => now()->subDays(2),
            'location' => 'Court A',
            'status' => 'Selesai',
            'score_team' => 3,
            'score_opponent' => 1
        ]);

        Statistic::create([
            'match_id' => $match->id,
            'player_id' => $this->playerProfile->id,
            'goals' => 2,
            'assists' => 1,
            'yellow_cards' => 0,
            'red_cards' => 0,
            'minutes_played' => 35
        ]);

        $response = $this->actingAs($this->playerUser)
            ->get('/v1/rian-pemain/players/' . $this->playerProfile->id);

        $response->assertStatus(200);
        $response->assertSee('Profil Detail Atlet');
        $response->assertSee('Rian Pemain');
        $response->assertSee('Flank');
        // Check stats values are loaded
        $response->assertSee('2'); // Goals
        $response->assertSee('1'); // Assists
        $response->assertSee('35'); // Minutes
    }

    public function test_manager_can_access_finance_export()
    {
        Finance::create([
            'team_id' => $this->team->id,
            'type' => 'Pemasukan',
            'amount' => 50000.00,
            'date' => now(),
            'category' => 'Iuran Pemain',
            'description' => 'Pembayaran Kas'
        ]);

        $response = $this->actingAs($this->managerUser)
            ->get('/v1/manager-hendra/finances/export');

        $response->assertStatus(200);
        $response->assertSee('Laporan Keuangan Kas Tim');
        $response->assertSee('FC Antigravity');
        $response->assertSee('Pembayaran Kas');
        $response->assertSee('50.000');
    }

    public function test_user_cannot_access_other_teams_slug()
    {
        // Create another team and user
        $team2 = Team::create(['name' => 'Galaxy Futsal', 'plan' => 'free']);
        $user2 = User::create([
            'name' => 'Boni Manager',
            'email' => 'manager2@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->managementRole->id,
            'team_id' => $team2->id,
            'slug' => 'boni-manager'
        ]);

        // Manager Hendra (FC Antigravity) tries to access Galaxy Futsal's route
        $response = $this->actingAs($this->managerUser)
            ->get('/v1/boni-manager/dashboard');

        // Middleware PrefixSlugMiddleware should redirect to their own correct slug
        $response->assertRedirect('/v1/manager-hendra/dashboard');
    }

    public function test_coach_can_create_tactic()
    {
        $response = $this->actingAs($this->coachUser)
            ->post('/v1/coach-ilham/tactical-board/save', [
                'title' => 'Taktik Baru',
                'description' => 'Penjelasan strategi',
                'formation' => '2-2',
                'canvas_data' => json_encode(['players' => [], 'opponents' => [], 'ball' => [], 'drawings' => []])
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        
        $this->assertDatabaseHas('tactics', [
            'team_id' => $this->team->id,
            'title' => 'Taktik Baru'
        ]);
    }

    public function test_coach_cannot_access_tactical_board_if_team_is_free()
    {
        $this->team->update(['plan' => 'free']);

        $response = $this->actingAs($this->coachUser)
            ->get('/v1/coach-ilham/tactical-board');

        $response->assertRedirect('/v1/coach-ilham/dashboard');
        $response->assertSessionHas('error');
    }

    public function test_coach_cannot_save_tactic_if_team_is_free()
    {
        $this->team->update(['plan' => 'free']);

        $response = $this->actingAs($this->coachUser)
            ->post('/v1/coach-ilham/tactical-board/save', [
                'title' => 'Taktik Baru',
                'description' => 'Penjelasan',
                'formation' => '2-2',
                'canvas_data' => json_encode([])
            ]);

        $response->assertStatus(403);
    }

    public function test_player_cannot_access_tactical_board()
    {
        $response = $this->actingAs($this->playerUser)
            ->get('/v1/rian-pemain/tactical-board');

        $response->assertStatus(403);
    }

    public function test_coach_can_mark_attendance()
    {
        $schedule = Schedule::create([
            'team_id' => $this->team->id,
            'title' => 'Latihan',
            'type' => 'Latihan',
            'start_time' => now()->addDay(),
            'location' => 'Court A',
            'dues_amount' => 10000
        ]);

        $response = $this->actingAs($this->coachUser)
            ->post('/v1/coach-ilham/schedules/' . $schedule->id . '/attendance', [
                'attendance' => [
                    $this->playerProfile->id => [
                        'status' => 'Hadir',
                        'notes' => 'Tepat waktu',
                        'is_dues_paid' => 1
                    ]
                ]
            ]);

        $response->assertRedirect('/v1/coach-ilham/schedules');
        
        $this->assertDatabaseHas('attendances', [
            'schedule_id' => $schedule->id,
            'player_id' => $this->playerProfile->id,
            'status' => 'Hadir',
            'is_dues_paid' => true
        ]);
    }

    public function test_tripay_callback_webhook_success_updates_team_to_premium()
    {
        $this->mock(\App\Services\TripayService::class, function ($mock) {
            $mock->shouldReceive('verifyCallback')->andReturn(true);
        });

        // Team 2 is currently on a free plan
        $team2 = Team::create(['name' => 'Galaxy Futsal', 'plan' => 'free']);
        $payment = \App\Models\PremiumPayment::create([
            'team_id' => $team2->id,
            'user_id' => $this->managerUser->id,
            'amount' => 100000.00,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'merchant_ref' => 'TRX-999',
            'reference' => 'T999'
        ]);

        $response = $this->postJson('/tripay/callback', [
            'merchant_ref' => 'TRX-999',
            'reference' => 'T999',
            'status' => 'PAID'
        ], [
            'X-Callback-Signature' => 'test-signature-value'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('premium_payments', [
            'merchant_ref' => 'TRX-999',
            'payment_status' => 'paid',
            'status' => 'approved'
        ]);

        $this->assertDatabaseHas('teams', [
            'id' => $team2->id,
            'plan' => 'premium'
        ]);
    }

    public function test_user_can_register_new_team_and_manager_account()
    {
        $response = $this->post('/register', [
            'team_name' => 'Persija Futsal',
            'name' => 'Andritany',
            'email' => 'andritany@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $this->assertDatabaseHas('teams', [
            'name' => 'Persija Futsal',
            'plan' => 'free'
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Andritany',
            'email' => 'andritany@test.com',
            'role_id' => $this->managementRole->id
        ]);

        $user = User::where('email', 'andritany@test.com')->first();
        $response->assertRedirect('/v1/' . $user->slug . '/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_free_team_cannot_exceed_finance_limits()
    {
        // Change team plan to free
        $this->team->update(['plan' => 'free']);

        // Create 10 finance entries
        for ($i = 0; $i < 10; $i++) {
            Finance::create([
                'team_id' => $this->team->id,
                'type' => 'Pemasukan',
                'amount' => 1000.00,
                'date' => now(),
                'category' => 'Iuran Pemain',
                'description' => 'Test ' . $i
            ]);
        }

        // Try to add 11th via POST
        $response = $this->actingAs($this->managerUser)
            ->post('/v1/manager-hendra/finances', [
                'type' => 'Pemasukan',
                'amount' => '10.000',
                'date' => date('Y-m-d'),
                'category' => 'Iuran Pemain',
                'description' => 'Limit breaker'
            ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('finances', [
            'description' => 'Limit breaker'
        ]);
    }

    public function test_superadmin_can_access_superadmin_dashboard()
    {
        $superadminUser = User::create([
            'name' => 'Admin Boss',
            'email' => 'boss@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->superadminRole->id,
            'slug' => 'superadmin'
        ]);

        $response = $this->actingAs($superadminUser)
            ->get('/v1/superadmin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Monitor Platform');
    }

    public function test_manager_cannot_access_superadmin_dashboard()
    {
        $response = $this->actingAs($this->managerUser)
            ->get('/v1/superadmin/dashboard');

        $response->assertStatus(403);
    }

    public function test_player_can_upload_dues_payment_receipt()
    {
        Storage::fake('google');

        $schedule = Schedule::create([
            'team_id' => $this->team->id,
            'title' => 'Latihan Rutin',
            'type' => 'Latihan',
            'start_time' => now()->addDay(),
            'location' => 'Court A',
            'dues_amount' => 15000
        ]);

        $file = \Illuminate\Http\UploadedFile::fake()->image('receipt.jpg');

        $response = $this->actingAs($this->playerUser)
            ->postJson('/v1/rian-pemain/schedules/' . $schedule->id . '/receipt', [
                'receipt' => $file
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Assert database has attendance record with payment receipt
        $this->assertDatabaseHas('attendances', [
            'schedule_id' => $schedule->id,
            'player_id' => $this->playerProfile->id,
            'status' => 'Hadir'
        ]);

        $attendance = Attendance::where('schedule_id', $schedule->id)
            ->where('player_id', $this->playerProfile->id)
            ->first();
            
        $this->assertNotNull($attendance->payment_receipt);
        
        // Assert file exists on the Google Drive disk
        $googlePath = str_replace('images/', '', $attendance->payment_receipt);
        Storage::disk('google')->assertExists($googlePath);
    }


    public function test_management_can_add_coach()
    {
        $response = $this->actingAs($this->managerUser)
            ->post('/v1/manager-hendra/coaches', [
                'name' => 'Coach Baru',
                'email' => 'newcoach@test.com',
                'password' => 'password123'
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('users', [
            'name' => 'Coach Baru',
            'email' => 'newcoach@test.com',
            'role_id' => $this->coachRole->id,
            'team_id' => $this->team->id
        ]);
    }

    public function test_coach_can_publish_announcement()
    {
        $response = $this->actingAs($this->coachUser)
            ->post('/v1/coach-ilham/announcements', [
                'title' => 'Pengumuman Penting',
                'content' => 'Isi pesan pengumuman taktis tim.'
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('announcements', [
            'team_id' => $this->team->id,
            'title' => 'Pengumuman Penting',
            'content' => 'Isi pesan pengumuman taktis tim.'
        ]);
    }

    public function test_coach_can_input_match_results_and_save_stats()
    {
        $match = MatchGame::create([
            'team_id' => $this->team->id,
            'opponent' => 'Lawan Baru FC',
            'date' => now(),
            'location' => 'Vidi Arena',
            'status' => 'Selesai',
            'score_team' => 4,
            'score_opponent' => 2
        ]);

        $response = $this->actingAs($this->coachUser)
            ->post('/v1/coach-ilham/matches/' . $match->id . '/stats', [
                'stats' => [
                    $this->playerProfile->id => [
                        'goals' => 3,
                        'assists' => 1,
                        'yellow_cards' => 0,
                        'red_cards' => 0,
                        'minutes_played' => 40
                    ]
                ]
            ]);

        $response->assertRedirect('/v1/coach-ilham/matches');
        
        $this->assertDatabaseHas('statistics', [
            'match_id' => $match->id,
            'player_id' => $this->playerProfile->id,
            'goals' => 3,
            'assists' => 1,
            'minutes_played' => 40
        ]);
    }

    public function test_upgrade_premium_tripay_payment_creation_redirect()
    {
        $this->mock(\App\Services\TripayService::class, function ($mock) {
            $mock->shouldReceive('verifyCallback')->andReturn(true);
            $mock->shouldReceive('getPaymentChannels')->andReturn([
                ['code' => 'QRIS2', 'name' => 'QRIS (Simulasi)', 'category' => 'Instant Payment', 'active' => true]
            ]);
            $mock->shouldReceive('createTransaction')->andReturn([
                'reference' => 'T9999',
                'instructions' => [],
                'qr_url' => 'http://example.com/qr',
                'pay_code' => '9999',
                'checkout_url' => 'http://example.com/checkout'
            ]);
        });

        $response = $this->actingAs($this->managerUser)
            ->post('/v1/manager-hendra/upgrade', [
                'payment_method' => 'QRIS2'
            ]);

        $response->assertRedirect();
        $this->assertStringContainsString('upgrade/payment/TRX-', $response->headers->get('Location'));
        
        $this->assertDatabaseHas('premium_payments', [
            'team_id' => $this->team->id,
            'payment_method' => 'QRIS2',
            'reference' => 'T9999'
        ]);
    }

    public function test_user_can_update_profile()
    {
        $response = $this->actingAs($this->managerUser)
            ->put('/v1/manager-hendra/settings/profile', [
                'name' => 'Hendra New Name',
                'email' => 'hendra_new@test.com'
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('users', [
            'id' => $this->managerUser->id,
            'name' => 'Hendra New Name',
            'email' => 'hendra_new@test.com'
        ]);
    }

    public function test_manager_can_close_account()
    {
        \Illuminate\Support\Facades\Mail::fake();

        $response = $this->actingAs($this->managerUser)
            ->post('/v1/manager-hendra/settings/profile/close');

        $response->assertRedirect('/login');
        
        $this->assertDatabaseHas('users', [
            'id' => $this->managerUser->id,
            'is_locked' => true
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->coachUser->id,
            'is_locked' => true
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->playerUser->id,
            'is_locked' => true
        ]);
        
        // Assert session has success message
        $response->assertSessionHas('success');

        // Verify login fails with locked credentials
        $loginResponse = $this->post('/login', [
            'email' => $this->managerUser->email,
            'password' => 'password'
        ]);
        
        $loginResponse->assertSessionHas('error_locked');
    }

    public function test_locked_user_is_logged_out_by_middleware()
    {
        // Lock the manager user
        $this->managerUser->update(['is_locked' => true]);

        // Attempt to access dashboard with active session
        $response = $this->actingAs($this->managerUser)
            ->get('/v1/manager-hendra/dashboard');

        // Middleware should catch it, log out and redirect to login
        $response->assertRedirect('/login');
        $this->assertFalse(\Illuminate\Support\Facades\Auth::check());
    }

    public function test_superadmin_can_lock_and_unlock_user()
    {
        \Illuminate\Support\Facades\Mail::fake();

        $superadmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->superadminRole->id,
            'slug' => 'superadmin'
        ]);

        // Superadmin locks user
        $response = $this->actingAs($superadmin)
            ->post("/v1/superadmin/users/{$this->managerUser->id}/toggle-lock");

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $this->managerUser->id,
            'is_locked' => true
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $this->coachUser->id,
            'is_locked' => true
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $this->playerUser->id,
            'is_locked' => true
        ]);

        // Superadmin unlocks user
        $response = $this->actingAs($superadmin)
            ->post("/v1/superadmin/users/{$this->managerUser->id}/toggle-lock");

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $this->managerUser->id,
            'is_locked' => false
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $this->coachUser->id,
            'is_locked' => false
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $this->playerUser->id,
            'is_locked' => false
        ]);

        // Assert that the email was sent to all team members
        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\AccountUnlockedMail::class, 3);
        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\AccountUnlockedMail::class, function ($mail) {
            return $mail->user->id === $this->managerUser->id;
        });
        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\AccountUnlockedMail::class, function ($mail) {
            return $mail->user->id === $this->coachUser->id;
        });
        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\AccountUnlockedMail::class, function ($mail) {
            return $mail->user->id === $this->playerUser->id;
        });
    }

    public function test_creating_match_schedule_automatically_creates_match_game()
    {
        $response = $this->actingAs($this->coachUser)
            ->post('/v1/coach-ilham/schedules', [
                'title' => 'Pertandingan Persahabatan',
                'type' => 'Pertandingan',
                'start_time' => '2026-06-25 15:00:00',
                'location' => 'Vidi Arena',
                'description' => 'Match schedule test',
                'opponent' => 'Lawan A FC',
                'dues_amount' => '15.000'
            ]);

        $response->assertRedirect();
        
        $schedule = Schedule::where('title', 'Pertandingan Persahabatan')->first();
        $this->assertNotNull($schedule);

        $this->assertDatabaseHas('matches', [
            'team_id' => $this->team->id,
            'schedule_id' => $schedule->id,
            'opponent' => 'Lawan A FC',
            'date' => '2026-06-25 00:00:00',
            'location' => 'Vidi Arena',
            'status' => 'Terjadwal'
        ]);
    }

    public function test_deleting_match_schedule_automatically_deletes_match_game()
    {
        $schedule = Schedule::create([
            'team_id' => $this->team->id,
            'title' => 'Pertandingan Spesial',
            'type' => 'Pertandingan',
            'start_time' => '2026-06-26 16:00:00',
            'location' => 'Vidi Arena',
            'opponent' => 'Lawan B FC'
        ]);

        $match = MatchGame::create([
            'team_id' => $this->team->id,
            'schedule_id' => $schedule->id,
            'opponent' => 'Lawan B FC',
            'date' => '2026-06-26',
            'location' => 'Vidi Arena',
            'status' => 'Terjadwal'
        ]);

        $response = $this->actingAs($this->coachUser)
            ->delete('/v1/coach-ilham/schedules/' . $schedule->id);

        $response->assertRedirect();

        $this->assertDatabaseMissing('schedules', ['id' => $schedule->id]);
        $this->assertDatabaseMissing('matches', ['id' => $match->id]);
    }

    public function test_updating_match_schedule_syncs_with_match_game()
    {
        $schedule = Schedule::create([
            'team_id' => $this->team->id,
            'title' => 'Pertandingan Lama',
            'type' => 'Pertandingan',
            'start_time' => '2026-06-27 17:00:00',
            'location' => 'Vidi Arena',
            'opponent' => 'Lawan C FC'
        ]);

        $match = MatchGame::create([
            'team_id' => $this->team->id,
            'schedule_id' => $schedule->id,
            'opponent' => 'Lawan C FC',
            'date' => '2026-06-27',
            'location' => 'Vidi Arena',
            'status' => 'Terjadwal'
        ]);

        $response = $this->actingAs($this->coachUser)
            ->put('/v1/coach-ilham/schedules/' . $schedule->id, [
                'title' => 'Pertandingan Baru',
                'type' => 'Pertandingan',
                'start_time' => '2026-06-28 18:00:00',
                'location' => 'Vidi Arena Edit',
                'opponent' => 'Lawan D FC',
                'dues_amount' => '10000'
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('matches', [
            'id' => $match->id,
            'opponent' => 'Lawan D FC',
            'date' => '2026-06-28 00:00:00',
            'location' => 'Vidi Arena Edit'
        ]);
    }

    public function test_player_can_scan_valid_qr_attendance()
    {
        $schedule = Schedule::create([
            'team_id' => $this->team->id,
            'title' => 'Latihan Tim',
            'type' => 'Latihan',
            'start_time' => now(),
            'location' => 'Champion Futsal',
            'dues_amount' => 10000
        ]);

        $url = \Illuminate\Support\Facades\URL::signedRoute('schedules.scan', [
            'slug' => $this->playerUser->slug, 
            'id' => $schedule->id
        ]);

        $response = $this->actingAs($this->playerUser)->get($url);

        $response->assertRedirect('/v1/' . $this->playerUser->slug . '/schedules');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'schedule_id' => $schedule->id,
            'player_id' => $this->playerProfile->id,
            'status' => 'Hadir'
        ]);
    }

    public function test_player_cannot_scan_invalid_qr_attendance()
    {
        $schedule = Schedule::create([
            'team_id' => $this->team->id,
            'title' => 'Latihan Tim',
            'type' => 'Latihan',
            'start_time' => now(),
            'location' => 'Champion Futsal',
        ]);

        $response = $this->actingAs($this->playerUser)
            ->get('/v1/' . $this->playerUser->slug . '/schedules/' . $schedule->id . '/scan');

        $response->assertStatus(403);
    }

    public function test_player_cannot_scan_qr_attendance_outside_12_hours()
    {
        $schedule = Schedule::create([
            'team_id' => $this->team->id,
            'title' => 'Latihan Tim',
            'type' => 'Latihan',
            'start_time' => now()->addDays(2),
            'location' => 'Champion Futsal',
        ]);

        $url = \Illuminate\Support\Facades\URL::signedRoute('schedules.scan', [
            'slug' => $this->playerUser->slug, 
            'id' => $schedule->id
        ]);

        $response = $this->actingAs($this->playerUser)->get($url);

        $response->assertRedirect('/v1/' . $this->playerUser->slug . '/schedules');
        $response->assertSessionHas('error');
        
        $this->assertDatabaseMissing('attendances', [
            'schedule_id' => $schedule->id,
            'player_id' => $this->playerProfile->id,
        ]);
    }

    public function test_non_player_cannot_scan_qr_attendance()
    {
        $schedule = Schedule::create([
            'team_id' => $this->team->id,
            'title' => 'Latihan Tim',
            'type' => 'Latihan',
            'start_time' => now(),
            'location' => 'Champion Futsal',
        ]);

        $url = \Illuminate\Support\Facades\URL::signedRoute('schedules.scan', [
            'slug' => $this->coachUser->slug, 
            'id' => $schedule->id
        ]);

        $response = $this->actingAs($this->coachUser)->get($url);

        $response->assertRedirect('/v1/' . $this->coachUser->slug . '/schedules');
        $response->assertSessionHas('error');
    }
}

