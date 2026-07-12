<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FutsalBoardUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_is_premium_helper()
    {
        $team = Team::make([
            'plan' => 'premium',
            'premium_until' => now()->addDays(5)
        ]);

        $this->assertTrue($team->isPremium());
        $this->assertFalse($team->isFree());
    }

    public function test_team_is_free_helper()
    {
        $team = Team::make([
            'plan' => 'free'
        ]);

        $this->assertFalse($team->isPremium());
        $this->assertTrue($team->isFree());
    }

    public function test_free_team_limits_for_players()
    {
        $team = Team::create(['name' => 'Free Team', 'plan' => 'free']);

        // Free team allows up to 7 players
        for ($i = 0; $i < 6; $i++) {
            Player::create([
                'team_id' => $team->id,
                'name' => "Player $i",
                'number' => $i + 1,
                'position' => 'Flank'
            ]);
        }

        // Count is 6, should be able to add
        $this->assertTrue($team->canAddPlayer());

        // Add 7th player
        Player::create([
            'team_id' => $team->id,
            'name' => "Player 7",
            'number' => 7,
            'position' => 'Flank'
        ]);

        // Count is 7, should NOT be able to add
        $this->assertFalse($team->canAddPlayer());
    }

    public function test_premium_team_has_no_limits_for_players()
    {
        $team = Team::create(['name' => 'Premium Team', 'plan' => 'premium']);

        // Create 8 players
        for ($i = 0; $i < 8; $i++) {
            Player::create([
                'team_id' => $team->id,
                'name' => "Player $i",
                'number' => $i + 1,
                'position' => 'Flank'
            ]);
        }

        // Premium team has no limit, should be true
        $this->assertTrue($team->canAddPlayer());
    }

    public function test_user_slug_generation_helper()
    {
        $role = Role::create(['name' => 'player', 'description' => 'Player']);
        
        $user1 = User::create([
            'name' => 'Budi Prasetyo',
            'email' => 'budi1@test.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
        ]);

        $user2 = User::create([
            'name' => 'Budi Prasetyo',
            'email' => 'budi2@test.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
        ]);

        $this->assertEquals('budi-prasetyo', $user1->slug);
        $this->assertEquals('budi-prasetyo-1', $user2->slug);
    }

    public function test_free_team_limits_for_coaches()
    {
        $role = Role::create(['name' => 'coach', 'description' => 'Coach']);
        $team = Team::create(['name' => 'Free Team', 'plan' => 'free']);

        // Free team can have max 1 coach
        $this->assertTrue($team->canAddCoach());

        User::create([
            'name' => 'Coach 1',
            'email' => 'coach1@test.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
            'team_id' => $team->id,
        ]);

        $this->assertFalse($team->canAddCoach());
    }


}
