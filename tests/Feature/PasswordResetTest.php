<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private $role;
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->role = Role::create(['name' => 'management', 'description' => 'Management']);
        $this->user = User::create([
            'name' => 'Hendra Manager',
            'email' => 'hendra@test.com',
            'password' => bcrypt('old_password'),
            'role_id' => $this->role->id,
            'slug' => 'hendra-manager'
        ]);
    }

    public function test_forgot_password_page_is_accessible()
    {
        $response = $this->get('/forgot-password');
        $response->assertStatus(200);
    }

    public function test_user_can_request_password_reset_link()
    {
        Mail::fake();

        $response = $this->post('/forgot-password', [
            'email' => 'hendra@test.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'hendra@test.com',
        ]);

        Mail::assertSent(\App\Mail\ResetPasswordMail::class, function ($mail) {
            return $mail->email === 'hendra@test.com' && $mail->user->id === $this->user->id;
        });
    }

    public function test_locked_user_cannot_request_password_reset()
    {
        $this->user->update(['is_locked' => true]);

        $response = $this->post('/forgot-password', [
            'email' => 'hendra@test.com',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_reset_password_page_is_accessible_with_token()
    {
        $token = 'test-token';
        $response = $this->get('/reset-password/' . $token . '?email=hendra@test.com');
        $response->assertStatus(200);
        $response->assertViewHas('token', $token);
        $response->assertViewHas('email', 'hendra@test.com');
    }

    public function test_user_can_reset_password_with_valid_token()
    {
        $token = 'my-secure-token';

        DB::table('password_reset_tokens')->insert([
            'email' => 'hendra@test.com',
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'hendra@test.com',
            'password' => 'new_password',
            'password_confirmation' => 'new_password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('success');

        // Check password updated
        $this->user->refresh();
        $this->assertTrue(Hash::check('new_password', $this->user->password));

        // Check token deleted
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => 'hendra@test.com',
        ]);
    }

    public function test_user_cannot_reset_password_with_invalid_token()
    {
        DB::table('password_reset_tokens')->insert([
            'email' => 'hendra@test.com',
            'token' => Hash::make('correct-token'),
            'created_at' => now(),
        ]);

        $response = $this->post('/reset-password', [
            'token' => 'invalid-token',
            'email' => 'hendra@test.com',
            'password' => 'new_password',
            'password_confirmation' => 'new_password',
        ]);

        $response->assertSessionHasErrors('email');

        // Check password NOT updated
        $this->user->refresh();
        $this->assertFalse(Hash::check('new_password', $this->user->password));
    }
}
