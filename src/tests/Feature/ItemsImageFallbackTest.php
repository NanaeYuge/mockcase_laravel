<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Item;

class ItemsImageFallbackTest extends TestCase
{
    use RefreshDatabase;

    /** 詳細ページ：image_pathがnullならno-image.pngが表示される */
    public function test_show_uses_no_image_when_image_path_is_null()
    {
        $item = Item::factory()->create([
            'image_path' => null, // 明示的に無し
            'name' => 'フォールバック詳細',
        ]);

        $res = $this->get(route('items.show', ['id' => $item->id]));

        $res->assertOk()
            ->assertSee('images/no-image.png') // srcに含まれる
            ->assertSee('フォールバック詳細');
    }

    /** 一覧ページ：image_pathがnullならno-image.pngが表示される */
    public function test_index_uses_no_image_when_image_path_is_null()
    {
        // 画像なし商品を1件だけ用意
        Item::factory()->create([
            'image_path' => null,
            'name' => 'フォールバック一覧',
        ]);

        $res = $this->get(route('items.index'));

        $res->assertOk()
            ->assertSee('images/no-image.png')
            ->assertSee('フォールバック一覧');
    }
}
