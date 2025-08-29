<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;

class MylistPaginationTest extends TestCase
{
    use RefreshDatabase;

    /** マイリストは1ページ7件でページングされる */
    public function test_mylist_paginates_seven_per_page()
    {
        $user = User::factory()->create();

        // 自分以外の出品者を1人作って、その人の商品20件を作る
        $seller = User::factory()->create();
        $items  = Item::factory()->count(20)->create(['user_id' => $seller->id]);

        // すべてをお気に入りに登録（hasMany のため attach ではなく create を使う）
        foreach ($items as $item) {
            $user->favorites()->create(['item_id' => $item->id]);
        }

        // 1ページ目：7件
        $res1 = $this->actingAs($user)->get('/?tab=mylist');
        $res1->assertOk();
        $this->assertSame(7, substr_count($res1->getContent(), 'class="item-card"'));

        // 2ページ目：7件
        $res2 = $this->actingAs($user)->get('/?tab=mylist&page=2');
        $res2->assertOk();
        $this->assertSame(7, substr_count($res2->getContent(), 'class="item-card"'));

        // 3ページ目：残り6件
        $res3 = $this->actingAs($user)->get('/?tab=mylist&page=3');
        $res3->assertOk();
        $this->assertSame(6, substr_count($res3->getContent(), 'class="item-card"'));
    }
}
