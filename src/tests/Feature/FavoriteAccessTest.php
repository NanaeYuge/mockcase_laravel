<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;

class FavoriteAccessTest extends TestCase
{
    use RefreshDatabase;

    /** ゲストはお気に入り登録できず、ログインへ */
    public function test_guest_cannot_favorite()
    {
        $item = Item::factory()->create();

        $res = $this->post(route('items.favorite', ['item' => $item->id]));
        $res->assertRedirect('/login');
    }

    /** ログインユーザーは登録できる */
    public function test_logged_in_user_can_favorite()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $this->actingAs($user);

        $res = $this->post(route('items.favorite', ['item' => $item->id]));
        $res->assertRedirect(); // 実装がリダイレクト想定

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /** ログインユーザーは解除できる */
    public function test_logged_in_user_can_unfavorite()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $this->actingAs($user);

        // 先に登録
        $this->post(route('items.favorite', ['item' => $item->id]));

        // 解除
        $res = $this->delete(route('items.unfavorite', ['item' => $item->id]));
        $res->assertRedirect();

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
}
