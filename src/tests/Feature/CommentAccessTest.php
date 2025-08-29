<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;

class CommentAccessTest extends TestCase
{
    use RefreshDatabase;

    /** ゲストはコメント投稿できず、ログインへリダイレクト */
    public function test_guest_cannot_post_comment()
    {
        $item = Item::factory()->create();

        $res = $this->post(route('comments.store', ['item' => $item->id]), [
            'content' => 'ゲストコメント',
        ]);

        $res->assertRedirect('/login');
    }

    /** ログインユーザーは投稿できる（正常系） */
    public function test_logged_in_user_can_post_comment()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $this->actingAs($user);

        $res = $this->post(
        route('comments.store', ['item' => $item->id]),
        ['content' => 'テストコメント'],
        [
        'X-Requested-With' => 'XMLHttpRequest',
        'Accept' => 'application/json',
        ]
        );
        $res->assertStatus(200);


        $res->assertStatus(200);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'テストコメント',
        ]);
    }

    public function test_comment_validation()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $this->actingAs($user);

        // ② バリデーション（必須）
        $res1 = $this->from(route('items.show', ['id' => $item->id]))
        ->post(route('comments.store', ['item' => $item->id]), ['content' => '']);
        $res1->assertRedirect(route('items.show', ['id' => $item->id]));
         // 旧: $this->assertSessionHasErrors(['content']);
        $res1->assertInvalid(['content']);

        // ② バリデーション（255超）
        $tooLong = str_repeat('あ', 256);
        $res2 = $this->from(route('items.show', ['id' => $item->id]))
        ->post(route('comments.store', ['item' => $item->id]), ['content' => $tooLong]);
        $res2->assertRedirect(route('items.show', ['id' => $item->id]));
        // 旧: $this->assertSessionHasErrors(['content']);
        $res2->assertInvalid(['content']);

    }
}


