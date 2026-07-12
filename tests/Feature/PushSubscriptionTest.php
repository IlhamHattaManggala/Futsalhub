<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PushSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    private $playerRole;
    private $team;
    private $playerUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->playerRole = Role::create(['name' => 'player', 'description' => 'Player']);
        $this->team = Team::create([
            'name' => 'FC Antigravity',
            'plan' => 'premium',
            'description' => 'Test Team'
        ]);

        $this->playerUser = User::create([
            'name' => 'Rian Pemain',
            'email' => 'player@test.com',
            'password' => bcrypt('password'),
            'role_id' => $this->playerRole->id,
            'team_id' => $this->team->id,
            'slug' => 'rian-pemain'
        ]);
    }

    public function test_guest_cannot_subscribe()
    {
        $response = $this->postJson('/v1/push-subscriptions', [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/abc',
            'keys' => [
                'p256dh' => 'random_p256dh_key',
                'auth' => 'random_auth_token'
            ]
        ]);

        $response->assertStatus(401);
    }

    public function test_user_can_subscribe_and_unsubscribe()
    {
        // 1. Subscribe
        $response = $this->actingAs($this->playerUser)
            ->postJson('/v1/push-subscriptions', [
                'endpoint' => 'https://fcm.googleapis.com/fcm/send/abc',
                'keys' => [
                    'p256dh' => 'random_p256dh_key',
                    'auth' => 'random_auth_token'
                ]
            ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Subscription saved successfully.']);

        $this->assertDatabaseHas('push_subscriptions', [
            'subscribable_id' => $this->playerUser->id,
            'subscribable_type' => User::class,
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/abc'
        ]);

        // 2. Unsubscribe
        $response = $this->actingAs($this->playerUser)
            ->deleteJson('/v1/push-subscriptions', [
                'endpoint' => 'https://fcm.googleapis.com/fcm/send/abc'
            ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Subscription deleted successfully.']);

        $this->assertDatabaseMissing('push_subscriptions', [
            'subscribable_id' => $this->playerUser->id,
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/abc'
        ]);
    }
}
