<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Models\Player;
use App\Models\TaskCategory;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PlayerTaskTest extends TestCase
{
    use RefreshDatabase;

    private $managementRole;
    private $coachRole;
    private $playerRole;
    private $team;
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

        // Create Coach
        $this->coachUser = User::create([
            'name' => 'Coach Ilham',
            'email' => 'coach@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->coachRole->id,
            'team_id' => $this->team->id,
            'slug' => 'coach-ilham'
        ]);

        // Create Player
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

        // Seed Category
        $this->category = TaskCategory::create(['name' => 'Fisik']);
    }

    public function test_coach_can_load_tasks_page_and_create_task()
    {
        $response = $this->actingAs($this->coachUser)
            ->get('/v1/coach-ilham/tasks');

        $response->assertStatus(200);
        $response->assertSee('Tugas Pemain');
        $response->assertSee('Buat Tugas Baru');

        // Create a task for all players
        $response = $this->actingAs($this->coachUser)
            ->post('/v1/coach-ilham/tasks', [
                'title' => 'Tidur Cepat',
                'task_category_id' => $this->category->id,
                'due_date' => now()->addDay()->format('Y-m-d H:i:s'),
                'description' => 'Tidur sebelum jam 10 malam',
                'assign_type' => 'all'
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('tasks', [
            'team_id' => $this->team->id,
            'title' => 'Tidur Cepat',
            'task_category_id' => $this->category->id
        ]);

        // Check pivot table assignment
        $this->assertDatabaseHas('player_tasks', [
            'player_id' => $this->playerProfile->id,
            'status' => 'Belum Selesai'
        ]);
    }

    public function test_player_can_start_task()
    {
        $task = Task::create([
            'team_id' => $this->team->id,
            'coach_id' => $this->coachUser->id,
            'task_category_id' => $this->category->id,
            'title' => 'Tidur Cepat',
            'due_date' => now()->addDay(),
        ]);

        DB::table('player_tasks')->insert([
            'task_id' => $task->id,
            'player_id' => $this->playerProfile->id,
            'status' => 'Belum Selesai',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Storage::fake('google');
        $base64Image = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=';

        $response = $this->actingAs($this->playerUser)
            ->postJson("/v1/rian-pemain/tasks/{$task->id}/start", [
                'start_proof_image' => $base64Image
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('player_tasks', [
            'task_id' => $task->id,
            'player_id' => $this->playerProfile->id,
            'status' => 'Mulai'
        ]);

        $assignment = DB::table('player_tasks')
            ->where('task_id', $task->id)
            ->where('player_id', $this->playerProfile->id)
            ->first();

        $this->assertNotNull($assignment->start_proof_image);
        $this->assertNotNull($assignment->started_at);

        $googlePath = str_replace('images/', '', $assignment->start_proof_image);
        Storage::disk('google')->assertExists($googlePath);
    }

    public function test_player_cannot_complete_task_without_starting()
    {
        $task = Task::create([
            'team_id' => $this->team->id,
            'coach_id' => $this->coachUser->id,
            'task_category_id' => $this->category->id,
            'title' => 'Tidur Cepat',
            'due_date' => now()->addDay(),
        ]);

        DB::table('player_tasks')->insert([
            'task_id' => $task->id,
            'player_id' => $this->playerProfile->id,
            'status' => 'Belum Selesai'
        ]);

        $base64Image = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=';

        $response = $this->actingAs($this->playerUser)
            ->postJson("/v1/rian-pemain/tasks/{$task->id}/complete", [
                'proof_image' => $base64Image
            ]);

        $response->assertStatus(400);
        $response->assertJson(['success' => false, 'message' => 'Anda harus memulai tugas terlebih dahulu sebelum menyelesaikannya.']);
    }

    public function test_player_cannot_complete_task_before_30_minutes()
    {
        $task = Task::create([
            'team_id' => $this->team->id,
            'coach_id' => $this->coachUser->id,
            'task_category_id' => $this->category->id,
            'title' => 'Latihan Mandiri',
            'due_date' => now()->addDay(),
        ]);

        DB::table('player_tasks')->insert([
            'task_id' => $task->id,
            'player_id' => $this->playerProfile->id,
            'status' => 'Mulai',
            'started_at' => now(), // started just now
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $base64Image = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=';

        $response = $this->actingAs($this->playerUser)
            ->postJson("/v1/rian-pemain/tasks/{$task->id}/complete", [
                'proof_image' => $base64Image
            ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['success', 'message']);
        $this->assertStringContainsString('Durasi latihan terlalu singkat', $response->json('message'));
    }

    public function test_player_can_complete_task_after_30_minutes()
    {
        $task = Task::create([
            'team_id' => $this->team->id,
            'coach_id' => $this->coachUser->id,
            'task_category_id' => $this->category->id,
            'title' => 'Latihan Mandiri',
            'due_date' => now()->addDay(),
        ]);

        DB::table('player_tasks')->insert([
            'task_id' => $task->id,
            'player_id' => $this->playerProfile->id,
            'status' => 'Mulai',
            'started_at' => now()->subMinutes(35), // started 35 minutes ago
            'created_at' => now()->subMinutes(35),
            'updated_at' => now()->subMinutes(35),
        ]);

        Storage::fake('google');
        $base64Image = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=';

        $response = $this->actingAs($this->playerUser)
            ->postJson("/v1/rian-pemain/tasks/{$task->id}/complete", [
                'proof_image' => $base64Image
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('player_tasks', [
            'task_id' => $task->id,
            'player_id' => $this->playerProfile->id,
            'status' => 'Selesai'
        ]);

        $assignment = DB::table('player_tasks')
            ->where('task_id', $task->id)
            ->where('player_id', $this->playerProfile->id)
            ->first();

        $this->assertNotNull($assignment->proof_image);
        $this->assertNotNull($assignment->completed_at);

        $googlePath = str_replace('images/', '', $assignment->proof_image);
        Storage::disk('google')->assertExists($googlePath);
    }

    public function test_player_cannot_complete_task_without_proof_image()
    {
        $task = Task::create([
            'team_id' => $this->team->id,
            'coach_id' => $this->coachUser->id,
            'task_category_id' => $this->category->id,
            'title' => 'Latihan Mandiri',
            'due_date' => now()->addDay(),
        ]);

        DB::table('player_tasks')->insert([
            'task_id' => $task->id,
            'player_id' => $this->playerProfile->id,
            'status' => 'Mulai',
            'started_at' => now()->subMinutes(35),
        ]);

        $response = $this->actingAs($this->playerUser)
            ->postJson("/v1/rian-pemain/tasks/{$task->id}/complete", []); // Empty payload

        $response->assertStatus(422); // Validation error
    }

    public function test_player_cannot_create_tasks()
    {
        $response = $this->actingAs($this->playerUser)
            ->post('/v1/rian-pemain/tasks', [
                'title' => 'Tidur Cepat',
                'task_category_id' => $this->category->id,
                'due_date' => now()->addDay()->format('Y-m-d H:i:s'),
                'assign_type' => 'all'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error'); // Error message saying only coach can create
    }

    public function test_coach_cannot_create_task_with_past_deadline()
    {
        $response = $this->actingAs($this->coachUser)
            ->post('/v1/coach-ilham/tasks', [
                'title' => 'Latihan Kemarin',
                'task_category_id' => $this->category->id,
                'due_date' => now()->subDay()->format('Y-m-d H:i:s'),
                'description' => 'Latihan yang sudah terlewat',
                'assign_type' => 'all'
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('due_date');
    }
}
