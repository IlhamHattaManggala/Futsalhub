<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Team;
use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagementToggleStatusTest extends TestCase
{
    use RefreshDatabase;

    private $roleManagement;
    private $rolePlayer;
    private $roleCoach;
    private $team;
    private $manager;
    private $playerUser;
    private $playerProfile;
    private $coachUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->roleManagement = Role::create(['name' => 'management', 'description' => 'Management']);
        $this->rolePlayer = Role::create(['name' => 'player', 'description' => 'Player']);
        $this->roleCoach = Role::create(['name' => 'coach', 'description' => 'Coach']);
        
        $this->team = Team::create([
            'name' => 'Futsal Club',
            'plan' => 'premium'
        ]);

        $this->manager = User::create([
            'name' => 'Manager Futsal',
            'email' => 'manager@futsal.com',
            'password' => bcrypt('password123'),
            'role_id' => $this->roleManagement->id,
            'team_id' => $this->team->id,
            'slug' => 'manager-futsal',
            'is_locked' => false
        ]);

        $this->playerUser = User::create([
            'name' => 'Player Futsal',
            'email' => 'player@futsal.com',
            'password' => bcrypt('password123'),
            'role_id' => $this->rolePlayer->id,
            'team_id' => $this->team->id,
            'slug' => 'player-futsal',
            'is_locked' => false
        ]);

        $this->playerProfile = Player::create([
            'team_id' => $this->team->id,
            'user_id' => $this->playerUser->id,
            'name' => 'Player Futsal',
            'number' => 10,
            'position' => 'Pivot'
        ]);

        $this->coachUser = User::create([
            'name' => 'Coach Futsal',
            'email' => 'coach@futsal.com',
            'password' => bcrypt('password123'),
            'role_id' => $this->roleCoach->id,
            'team_id' => $this->team->id,
            'slug' => 'coach-futsal',
            'is_locked' => false
        ]);
    }

    public function test_manager_can_toggle_player_status()
    {
        $this->assertFalse($this->playerUser->is_locked);

        // Turn OFF
        $response = $this->actingAs($this->manager)
            ->post("/v1/manager-futsal/players/{$this->playerProfile->id}/toggle-status");

        $response->assertRedirect();
        $this->playerUser->refresh();
        $this->assertTrue($this->playerUser->is_locked);

        // Turn ON
        $response = $this->actingAs($this->manager)
            ->post("/v1/manager-futsal/players/{$this->playerProfile->id}/toggle-status");

        $response->assertRedirect();
        $this->playerUser->refresh();
        $this->assertFalse($this->playerUser->is_locked);
    }

    public function test_manager_cannot_toggle_player_without_account()
    {
        $playerWithoutAccount = Player::create([
            'team_id' => $this->team->id,
            'user_id' => null,
            'name' => 'Solo Player',
            'number' => 7,
            'position' => 'Anchor'
        ]);

        $response = $this->actingAs($this->manager)
            ->post("/v1/manager-futsal/players/{$playerWithoutAccount->id}/toggle-status");

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_manager_can_toggle_coach_status()
    {
        $this->assertFalse($this->coachUser->is_locked);

        // Turn OFF
        $response = $this->actingAs($this->manager)
            ->post("/v1/manager-futsal/coaches/{$this->coachUser->id}/toggle-status");

        $response->assertRedirect();
        $this->coachUser->refresh();
        $this->assertTrue($this->coachUser->is_locked);

        // Turn ON
        $response = $this->actingAs($this->manager)
            ->post("/v1/manager-futsal/coaches/{$this->coachUser->id}/toggle-status");

        $response->assertRedirect();
        $this->coachUser->refresh();
        $this->assertFalse($this->coachUser->is_locked);
    }

    public function test_player_cannot_toggle_status()
    {
        $response = $this->actingAs($this->playerUser)
            ->post("/v1/manager-futsal/players/{$this->playerProfile->id}/toggle-status");
        $response->assertStatus(403);

        $response2 = $this->actingAs($this->playerUser)
            ->post("/v1/manager-futsal/coaches/{$this->coachUser->id}/toggle-status");
        $response2->assertStatus(403);
    }
}
