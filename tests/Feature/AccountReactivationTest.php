<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AccountReactivationTest extends TestCase
{
    use RefreshDatabase;

    private $roleManagement;
    private $rolePlayer;
    private $team;
    private $manager;
    private $player;

    protected function setUp(): void
    {
        parent::setUp();

        $this->roleManagement = Role::create(['name' => 'management', 'description' => 'Management']);
        $this->rolePlayer = Role::create(['name' => 'player', 'description' => 'Player']);
        
        $this->team = Team::create([
            'name' => 'Depok FC',
            'plan' => 'free'
        ]);

        $this->manager = User::create([
            'name' => 'Manager Depok',
            'email' => 'manager@depok.com',
            'password' => bcrypt('password123'),
            'role_id' => $this->roleManagement->id,
            'team_id' => $this->team->id,
            'slug' => 'manager-depok'
        ]);

        $this->player = User::create([
            'name' => 'Player Depok',
            'email' => 'player@depok.com',
            'password' => bcrypt('password123'),
            'role_id' => $this->rolePlayer->id,
            'team_id' => $this->team->id,
            'slug' => 'player-depok'
        ]);
    }

    public function test_manager_closing_account_locks_team()
    {
        Mail::fake();

        $response = $this->actingAs($this->manager)
            ->post('/v1/manager-depok/settings/profile/close');

        $response->assertRedirect('/login');
        $response->assertSessionHas('success', 'Akun Anda beserta seluruh anggota tim telah berhasil ditutup.');

        // Assert manager and player are locked
        $this->manager->refresh();
        $this->player->refresh();
        $this->assertTrue($this->manager->is_locked);
        $this->assertTrue($this->player->is_locked);

        // Assert email was NOT sent
        Mail::assertNotSent(\App\Mail\ReactivateAccountMail::class);
    }

    public function test_login_with_locked_account_shows_reactivation_prompt_and_sends_email()
    {
        Mail::fake();
        // Lock user
        $this->manager->update(['is_locked' => true]);

        $response = $this->post('/login', [
            'email' => 'manager@depok.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error_locked');
        $response->assertSessionHas('locked_email', 'manager@depok.com');

        // Assert email was sent automatically on login attempt
        Mail::assertSent(\App\Mail\ReactivateAccountMail::class, function ($mail) {
            return $mail->user->id === $this->manager->id && str_contains($mail->reactivateUrl, 'account/reactivate');
        });
    }

    public function test_send_reactivation_email_manually()
    {
        Mail::fake();
        $this->manager->update(['is_locked' => true]);

        $response = $this->post('/account/reactivate/send', [
            'email' => 'manager@depok.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Mail::assertSent(\App\Mail\ReactivateAccountMail::class, function ($mail) {
            return $mail->user->id === $this->manager->id;
        });
    }

    public function test_clicking_signed_reactivate_link_unlocks_team()
    {
        // Lock team
        $this->manager->update(['is_locked' => true]);
        $this->player->update(['is_locked' => true]);

        // Generate signed URL
        $signedUrl = URL::signedRoute('account.reactivate', ['id' => $this->manager->id]);

        $response = $this->get($signedUrl);

        $response->assertRedirect('/login');
        $response->assertSessionHas('success');

        // Assert team is unlocked
        $this->manager->refresh();
        $this->player->refresh();
        $this->assertFalse($this->manager->is_locked);
        $this->assertFalse($this->player->is_locked);
    }
}
