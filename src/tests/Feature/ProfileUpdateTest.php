<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function profile_edit_page_is_accessible_when_logged_in()
    {
        $user = User::factory()->create();

        $res = $this->actingAs($user)->get(route('profile.edit')); // GET /mypage/profile
        $res->assertOk()
            ->assertSee('プロフィール') // 画面の見出しなどに合わせて調整OK
            ->assertSee($user->email);
    }

    /** @test */
    public function user_can_update_profile_basic_fields()
    {
        $user = User::factory()->create([
            'name'        => '旧名前',
            'email'       => 'old@example.com',
            'postal_code' => '000-0000',
            'address'     => '旧住所',
            'building'    => '旧ビル',
        ]);

        $payload = [
            'name'        => '新しい名前',
            'email'       => 'new@example.com',
            'postal_code' => '111-2222',
            'address'     => '新住所1-2-3',
            'building'    => '新ビル1001',
        ];

        // PUT /mypage/profile
        $res = $this->actingAs($user)->put(route('profile.update'), $payload);
        $res->assertRedirect(); // 成功時にどこかへリダイレクト（/mypage 等）

        $this->assertDatabaseHas('users', array_merge(['id' => $user->id], $payload));
    }

    /** @test */
    public function profile_update_requires_minimum_fields()
    {
        $user = User::factory()->create();

        $res = $this->actingAs($user)
            ->from(route('profile.edit'))            // バリデーション失敗時の戻り先
            ->put(route('profile.update'), [         // PUT /mypage/profile
                'name'        => '',
                'email'       => 'not-an-email',
                'postal_code' => '',
                'address'     => '',
                'building'    => '', // nullable ならOK
            ]);

        $res->assertRedirect(route('profile.edit'));
        $res->assertSessionHasErrors(['name', 'email', 'postal_code', 'address']);
    }
}
