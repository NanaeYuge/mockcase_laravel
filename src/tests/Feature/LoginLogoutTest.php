<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class LoginLogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_requires_email_and_password()
    {
        $res1 = $this->from(route('login'))->post('/login', ['email' => '', 'password' => 'x']);
        $res1->assertRedirect(route('login'))->assertInvalid(['email']);

        $res2 = $this->from(route('login'))->post('/login', ['email' => 'a@example.com', 'password' => '']);
        $res2->assertRedirect(route('login'))->assertInvalid(['password']);
    }

    public function test_login_with_wrong_credentials_shows_error()
    {
        $res = $this->from(route('login'))->post('/login', [
            'email' => 'notfound@example.com',
            'password' => 'password123',
        ]);

        $res->assertRedirect(route('login'));
        // 具体メッセージが画面に出る仕様なら GET して assertSee('ログイン情報が登録されていません')
    }

    public function test_login_with_valid_credentials_succeeds()
    {
        $user = User::factory()->create([
            'email' => 'ok@example.com',
            'password' => bcrypt('password123')
        ]);

        $res = $this->post('/login', [
            'email' => 'ok@example.com',
            'password' => 'password123',
        ]);

        $res->assertRedirect(route('home')); // 実装に合わせて
        $this->assertAuthenticatedAs($user);
    }

    public function test_logout_succeeds()
    {
        $user = User::factory()->create();
        $this->be($user);

        $res = $this->post('/logout'); // 画面のリンクはGETでも、実処理はPOST想定
        $res->assertRedirect('/');     // 実装に合わせて
        $this->assertGuest();
    }
}

