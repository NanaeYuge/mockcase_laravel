<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Item;

class ItemsSearchFilterTest extends TestCase
{
    use RefreshDatabase;

    /** キーワード検索で一致する商品のみ表示されること */
    public function test_search_filters_items_by_keyword()
    {
        // 名前にABCを含む商品
        $match = Item::factory()->create(['name' => 'テストABC商品']);
        // 名前にABCを含まない商品
        $noMatch = Item::factory()->create(['name' => 'テストXYZ商品']);

        // 検索: keyword=ABC
        $res = $this->get(route('items.index', ['keyword' => 'ABC']));

        $res->assertOk()
            ->assertSee($match->name)      // 含まれる
            ->assertDontSee($noMatch->name); // 含まれない
    }
}
