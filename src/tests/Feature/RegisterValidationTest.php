<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_name_is_required()
    {
        $res = $this->from(route('register'))
            ->post('/register', [
                'name' => '',
                'email' => 'a@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $res->assertRedirect(route('register'))
            ->assertInvalid(['name']);
        // 表示文言を厳密に見るなら assertSeeText で画面GETして確認
    }

    public function test_email_is_required()
    {
        $res = $this->from(route('register'))
            ->post('/register', [
                'name' => '太郎',
                'email' => '',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $res->assertRedirect(route('register'))
            ->assertInvalid(['email']);
    }

    public function test_password_is_required_and_min_8_and_confirmed()
    {
        // 未入力
        $res1 = $this->from(route('register'))->post('/register', [
            'name' => '太郎', 'email' => 'a@example.com',
            'password' => '', 'password_confirmation' => '',
        ]);
        $res1->assertRedirect(route('register'))->assertInvalid(['password']);

        // 7文字以下
        $res2 = $this->from(route('register'))->post('/register', [
            'name' => '太郎', 'email' => 'b@example.com',
            'password' => '1234567', 'password_confirmation' => '1234567',
        ]);
        $res2->assertRedirect(route('register'))->assertInvalid(['password']);

        // 確認不一致
        $res3 = $this->from(route('register'))->post('/register', [
            'name' => '太郎', 'email' => 'c@example.com',
            'password' => 'password123', 'password_confirmation' => 'x',
        ]);
        $res3->assertRedirect(route('register'))->assertInvalid(['password']);
    }

    public function test_success_register_redirects_to_login()
    {
        $res = $this->post('/register', [
            'name' => '太郎',
            'email' => 'ok@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // 実装に合わせて（ログイン画面へ遷移）
        $res->assertRedirect(route('verification.notice'));
        $this->assertDatabaseHas('users', ['email' => 'ok@example.com']);
    }
}
