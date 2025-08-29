<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;

class ItemsShowCountsTest extends TestCase
{
    use RefreshDatabase;

    /** いいね登録で♡数が1になる */
    public function test_favorite_count_increases_on_detail()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // まだ0表示
        $this->get(route('items.show', ['id' => $item->id]))
            ->assertOk()
            ->assertSee('♡ 0');

        // いいね登録→詳細再表示で1表示
        $this->actingAs($user)
            ->post(route('items.favorite', ['item' => $item->id]))
            ->assertRedirect();

        $this->get(route('items.show', ['id' => $item->id]))
            ->assertOk()
            ->assertSee('♡ 1');
    }

    /** コメント投稿で💬数が1になる */
    public function test_comment_count_increases_on_detail()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // まだ0表示
        $this->get(route('items.show', ['id' => $item->id]))
            ->assertOk()
            ->assertSee('💬 0');

        // コメント投稿→詳細再表示で1表示
        $this->actingAs($user)
            ->post(route('comments.store', ['item' => $item->id]), [
                'content' => 'テストコメント',
            ]);

        $this->get(route('items.show', ['id' => $item->id]))
            ->assertOk()
            ->assertSee('💬 1');
    }
}
