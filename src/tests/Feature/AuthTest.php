<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_email_required()
    {
        $res = $this->post('/login', [
            'email' => '',
            'password' => 'password',
        ]);
        $res->assertSessionHasErrors(['email']);
    }

    public function test_login_password_required()
    {
        $res = $this->post('/login', [
            'email' => 'a@example.com',
            'password' => '',
        ]);
        $res->assertSessionHasErrors(['password']);
    }

    public function test_login_invalid_credentials()
    {
        $res = $this->post('/login', [
            'email' => 'no@no.com',
            'password' => 'wrong',
        ]);
        $res->assertSessionHasErrors(); // メッセージ文言は実装依存
        $this->assertFalse(Auth::check());
    }

    public function test_login_success()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $res = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $res->assertRedirect('/');
        $this->assertTrue(Auth::check());
    }

    public function test_logout()
    {
        $user = User::factory()->create();
        $this->be($user);

        $res = $this->post('/logout');
        $res->assertRedirect('/'); // 実装に合わせて
        $this->assertFalse(Auth::check());
    }
}
