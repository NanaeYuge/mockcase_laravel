<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Item;
use App\Models\Category;

class ItemsShowCategoriesTest extends TestCase
{
    use RefreshDatabase;

    /** 商品詳細で、複数カテゴリが表示されること */
    public function test_multiple_categories_are_shown_on_item_detail()
    {
        // カテゴリ2件
        $cat1 = Category::factory()->create(['name' => 'カテゴリA']);
        $cat2 = Category::factory()->create(['name' => 'カテゴリB']);

        // 商品
        $item = Item::factory()->create();

        // 多対多のアタッチ（ピボット/キー名は実装に合わせる）
        $item->categories()->attach([$cat1->id, $cat2->id]);

        // 詳細ページで両方表示される
        $res = $this->get(route('items.show', ['id' => $item->id]));
        $res->assertOk()
            ->assertSee('カテゴリA')
            ->assertSee('カテゴリB');
        // 並び順を保証している実装なら：
        // $res->assertSeeInOrder(['カテゴリA', 'カテゴリB']);
    }
}
