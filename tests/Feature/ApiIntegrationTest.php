<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Models\Player;
use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\TaskCategory;
use App\Models\Task;
use App\Models\MatchGame;
use App\Models\Finance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ApiIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private $managementRole;
    private $coachRole;
    private $playerRole;
    private $team;
    private $managementUser;
    private $coachUser;
    private $playerUser;
    private $playerProfile;
    private $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed Roles
        $this->managementRole = Role::create(['name' => 'management', 'description' => 'Management']);
        $this->coachRole = Role::create(['name' => 'coach', 'description' => 'Coach']);
        $this->playerRole = Role::create(['name' => 'player', 'description' => 'Player']);

        // Create Team
        $this->team = Team::create([
            'name' => 'FC Antigravity',
            'plan' => 'premium',
            'description' => 'Test Team'
        ]);

        // Create Users
        $this->managementUser = User::create([
            'name' => 'Manager Roni',
            'email' => 'manager@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->managementRole->id,
            'team_id' => $this->team->id,
            'slug' => 'fc-antigravity-manager'
        ]);

        $this->coachUser = User::create([
            'name' => 'Coach Ilham',
            'email' => 'coach@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->coachRole->id,
            'team_id' => $this->team->id,
            'slug' => 'fc-antigravity-coach'
        ]);

        $this->playerUser = User::create([
            'name' => 'Rian Pemain',
            'email' => 'player@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->playerRole->id,
            'team_id' => $this->team->id,
            'slug' => 'fc-antigravity-player'
        ]);

        $this->playerProfile = Player::create([
            'team_id' => $this->team->id,
            'user_id' => $this->playerUser->id,
            'name' => 'Rian Pemain',
            'number' => 7,
            'position' => 'Flank'
        ]);

        // Seed Category
        $this->category = TaskCategory::create(['name' => 'Fisik']);
    }

    public function test_api_login_correct_credentials()
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => 'player@test.com',
            'password' => 'password'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Login berhasil.'
        ]);
        $response->assertJsonStructure([
            'data' => [
                'token',
                'user' => [
                    'id', 'name', 'email', 'role', 'slug', 'team_id', 'team_name'
                ]
            ]
        ]);
    }

    public function test_api_login_incorrect_credentials()
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => 'player@test.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'Email atau password yang Anda masukkan salah.'
        ]);
    }

    public function test_api_unauthenticated_request_fails()
    {
        $response = $this->getJson('/api/v1/me');
        $response->assertStatus(401);
    }

    public function test_api_me_returns_profile_for_authenticated_user()
    {
        $response = $this->actingAs($this->playerUser, 'sanctum')
            ->getJson('/api/v1/me');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'user' => [
                    'email' => 'player@test.com',
                    'role' => 'player'
                ]
            ]
        ]);
    }

    public function test_api_tenant_slug_validation()
    {
        // Try accessing with incorrect tenant slug, should fail with 403
        $response = $this->actingAs($this->playerUser, 'sanctum')
            ->getJson('/api/v1/wrong-slug/schedules');

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Unauthorized slug.'
        ]);

        // Accessing with correct slug should succeed
        $response = $this->actingAs($this->playerUser, 'sanctum')
            ->getJson('/api/v1/fc-antigravity-player/schedules');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);
    }

    public function test_player_can_view_schedules_and_submit_receipt()
    {
        $schedule = Schedule::create([
            'team_id' => $this->team->id,
            'title' => 'Latihan Rutin',
            'type' => 'Latihan',
            'start_time' => now()->addDay(),
            'location' => 'Indo Futsal',
            'dues_amount' => 20000
        ]);

        $response = $this->actingAs($this->playerUser, 'sanctum')
            ->getJson("/api/v1/fc-antigravity-player/schedules");

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data.schedules');

        // Submit payment receipt
        Storage::fake('google');
        $file = UploadedFile::fake()->image('receipt.png');

        $response = $this->actingAs($this->playerUser, 'sanctum')
            ->postJson("/api/v1/fc-antigravity-player/schedules/{$schedule->id}/receipt", [
                'receipt' => $file
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Bukti pembayaran berhasil dikirim! Menunggu verifikasi dari pelatih.'
        ]);

        // Assert file exists on the Google Drive disk
        $att = Attendance::where('player_id', $this->playerProfile->id)->where('schedule_id', $schedule->id)->first();
        $this->assertNotNull($att->payment_receipt);
        $googlePath = str_replace('images/', '', $att->payment_receipt);
        Storage::disk('google')->assertExists($googlePath);
    }


    public function test_player_can_complete_task_via_api()
    {
        $task = Task::create([
            'team_id' => $this->team->id,
            'coach_id' => $this->coachUser->id,
            'task_category_id' => $this->category->id,
            'title' => 'Tidur Lebih Cepat',
            'due_date' => now()->addDay(),
        ]);

        DB::table('player_tasks')->insert([
            'task_id' => $task->id,
            'player_id' => $this->playerProfile->id,
            'status' => 'Belum Selesai',
        ]);

        Storage::fake('google');
        $file = UploadedFile::fake()->image('proof.jpg');

        $response = $this->actingAs($this->playerUser, 'sanctum')
            ->postJson("/api/v1/fc-antigravity-player/tasks/{$task->id}/complete", [
                'proof_image' => $file
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Tugas berhasil diselesaikan! Foto bukti telah dikirim.'
        ]);

        // Assert file exists on the Google Drive disk
        $pivot = DB::table('player_tasks')->where('task_id', $task->id)->where('player_id', $this->playerProfile->id)->first();
        $this->assertNotNull($pivot->proof_image);
        $googlePath = str_replace('images/', '', $pivot->proof_image);
        Storage::disk('google')->assertExists($googlePath);
    }


    public function test_coach_can_create_schedule_and_save_attendance()
    {
        $response = $this->actingAs($this->coachUser, 'sanctum')
            ->postJson("/api/v1/fc-antigravity-coach/schedules", [
                'title' => 'Latihan Taktik',
                'type' => 'Latihan',
                'start_time' => now()->addDay()->format('Y-m-d H:i:s'),
                'location' => 'Arena Futsal',
                'dues_amount' => 0
            ]);

        $response->assertStatus(201);
        $scheduleId = $response->json('data.schedule.id');

        // Save attendance
        $response = $this->actingAs($this->coachUser, 'sanctum')
            ->postJson("/api/v1/fc-antigravity-coach/schedules/{$scheduleId}/attendance", [
                'attendance' => [
                    [
                        'player_id' => $this->playerProfile->id,
                        'status' => 'Hadir',
                        'notes' => 'Tepat waktu',
                        'is_dues_paid' => true
                    ]
                ]
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    public function test_management_can_manage_roster_and_finances()
    {
        // 1. Add Player
        $response = $this->actingAs($this->managementUser, 'sanctum')
            ->postJson("/api/v1/fc-antigravity-manager/players", [
                'name' => 'Faisal Baru',
                'number' => 10,
                'position' => 'Pivot',
                'create_account' => false
            ]);

        $response->assertStatus(201);
        $newPlayerId = $response->json('data.player.id');

        // 2. Record Finance
        $response = $this->actingAs($this->managementUser, 'sanctum')
            ->postJson("/api/v1/fc-antigravity-manager/finances", [
                'type' => 'Pemasukan',
                'amount' => '150000',
                'date' => now()->format('Y-m-d'),
                'category' => 'Iuran Mandiri',
                'description' => 'Iuran iuran rutin tim'
            ]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true
        ]);
    }

    public function test_role_restrictions_on_endpoints()
    {
        // Player tries to post finance, should return 403 Unauthorized access.
        $response = $this->actingAs($this->playerUser, 'sanctum')
            ->postJson("/api/v1/fc-antigravity-player/finances", [
                'type' => 'Pemasukan',
                'amount' => '50000',
                'date' => now()->format('Y-m-d'),
                'category' => 'Uang Kas',
                'description' => 'Pemain coba nulis kas'
            ]);

        $response->assertStatus(403);
    }
}
