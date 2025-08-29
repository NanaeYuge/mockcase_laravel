<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** 名前未入力でバリデーション */
    public function test_name_required()
    {
        $res = $this->post('/register', [
            'name' => '',
            'email' => 'a@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $res->assertSessionHasErrors(['name']);
    }

    /** メール未入力 */
    public function test_email_required()
    {
        $res = $this->post('/register', [
            'name' => '太郎',
            'email' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $res->assertSessionHasErrors(['email']);
    }

    /** パスワード未入力 */
    public function test_password_required()
    {
        $res = $this->post('/register', [
            'name' => '太郎',
            'email' => 'a@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);
        $res->assertSessionHasErrors(['password']);
    }

    /** パスワード7文字以下 */
    public function test_password_min_8()
    {
        $res = $this->post('/register', [
            'name' => '太郎',
            'email' => 'a@example.com',
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ]);
        $res->assertSessionHasErrors(['password']);
    }

    /** 確認用と不一致 */
    public function test_password_confirmation_must_match()
    {
        $res = $this->post('/register', [
            'name' => '太郎',
            'email' => 'a@example.com',
            'password' => 'password',
            'password_confirmation' => 'different',
        ]);
        $res->assertSessionHasErrors(['password']);
    }

    public function test_register_success()
    {
    $res = $this->post('/register', [
        'name' => '太郎',
        'email' => 'a@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    // メール確認必須の挙動に合わせる
    $res->assertRedirect('/email/verify');

    // 追加の確認（任意）
    $this->assertDatabaseHas('users', ['email' => 'a@example.com']);
    $this->assertAuthenticated(); // 登録後はログイン状態（未確認）であること
    }
}
