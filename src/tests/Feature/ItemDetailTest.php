<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_detail_shows_required_fields()
    {
        $seller = User::factory()->create();
        $item = Item::factory()->for($seller)->create([
            'name' => '高級トースター',
            'price' => 12345,
            'description' => '説明文です',
            'condition' => 2, // 未使用に近い
        ]);

        $catA = Category::factory()->create(['name' => '家電']);
        $catB = Category::factory()->create(['name' => 'キッチン']);
        $item->categories()->attach([$catA->id, $catB->id]);

        // コメント
        $u1 = User::factory()->create(['name' => '花子']);
        $u2 = User::factory()->create(['name' => '次郎']);
        Comment::factory()->create(['item_id' => $item->id, 'user_id' => $u1->id, 'content' => 'いいですね']);
        Comment::factory()->create(['item_id' => $item->id, 'user_id' => $u2->id, 'content' => '欲しいです']);

        $res = $this->get(route('items.show', $item->id));
        $res->assertOk()
            ->assertSee('高級トースター')
            ->assertSee('12,345')
            ->assertSee('説明文です')
            ->assertSee('未使用に近い') // conditionラベル
            ->assertSee('家電')
            ->assertSee('キッチン')
            ->assertSee('花子')
            ->assertSee('次郎')
            ->assertSee('いいですね')
            ->assertSee('欲しいです');
    }
}
