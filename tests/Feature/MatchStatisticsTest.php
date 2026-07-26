<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Team;
use App\Models\Player;
use App\Models\MatchGame;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchStatisticsTest extends TestCase
{
    use RefreshDatabase;

    private $roleCoach;
    private $team;
    private $coachUser;
    private $playerProfile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->roleCoach = Role::create(['name' => 'coach', 'description' => 'Coach']);
        
        $this->team = Team::create([
            'name' => 'Futsal Club',
            'plan' => 'premium'
        ]);

        $this->coachUser = User::create([
            'name' => 'Coach Futsal',
            'email' => 'coach@futsal.com',
            'password' => bcrypt('password123'),
            'role_id' => $this->roleCoach->id,
            'team_id' => $this->team->id,
            'slug' => 'coach-futsal'
        ]);

        $this->playerProfile = Player::create([
            'team_id' => $this->team->id,
            'name' => 'Goalkeeper Futsal',
            'number' => 1,
            'position' => 'Goalkeeper'
        ]);
    }

    public function test_coach_can_save_extended_match_and_player_statistics()
    {
        $match = MatchGame::create([
            'team_id' => $this->team->id,
            'opponent' => 'Lawan FC',
            'date' => now(),
            'location' => 'Depok Court',
            'status' => 'Selesai',
            'score_team' => 3,
            'score_opponent' => 1
        ]);

        $response = $this->actingAs($this->coachUser)
            ->post("/v1/coach-futsal/matches/{$match->id}/stats", [
                'possession_team' => 60,
                'possession_opponent' => 40,
                'shoot_on_target_team' => 12,
                'shoot_on_target_opponent' => 5,
                'shoot_off_target_team' => 8,
                'shoot_off_target_opponent' => 4,
                'stats' => [
                    $this->playerProfile->id => [
                        'goals' => 0,
                        'assists' => 0,
                        'yellow_cards' => 0,
                        'red_cards' => 0,
                        'minutes_played' => 40,
                        'clearance' => 3,
                        'save' => 7,
                        'shoot_on_target' => 0,
                        'shoot_off_target' => 0,
                    ]
                ]
            ]);

        $response->assertRedirect('/v1/coach-futsal/matches');

        // Assert team stats are updated
        $match->refresh();
        $this->assertEquals(60, $match->possession_team);
        $this->assertEquals(40, $match->possession_opponent);
        $this->assertEquals(12, $match->shoot_on_target_team);
        $this->assertEquals(5, $match->shoot_on_target_opponent);
        $this->assertEquals(8, $match->shoot_off_target_team);
        $this->assertEquals(4, $match->shoot_off_target_opponent);

        // Assert individual player stats are saved
        $this->assertDatabaseHas('statistics', [
            'match_id' => $match->id,
            'player_id' => $this->playerProfile->id,
            'clearance' => 3,
            'save' => 7,
            'shoot_on_target' => 0,
            'shoot_off_target' => 0
        ]);
    }
}
