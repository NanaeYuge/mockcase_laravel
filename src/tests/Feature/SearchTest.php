<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_by_name_like()
    {
        Item::factory()->create(['name' => 'りんごジュース']);
        Item::factory()->create(['name' => 'オレンジ']);

        $res = $this->get(route('items.index', ['keyword' => 'りんご']));
        $res->assertOk()
            ->assertSee('りんごジュース')
            ->assertDontSee('オレンジ');
    }

    public function test_keyword_is_kept_on_mylist_tab()
    {
        $user = User::factory()->create();
        $this->be($user);

        // 検索状態でマイリストへ（UIで保持＝クエリストリング維持）
        $res = $this->get('/?keyword=テスト&tab=mylist');
        $res->assertOk()->assertSee('テスト');
    }
}
