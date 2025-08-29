<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;

class SearchStateKeepTest extends TestCase
{
    use RefreshDatabase;

    public function test_keyword_kept_when_switching_to_mylist_tab()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();
        $this->actingAs($user);

        // お気に入り候補
        $match = Item::factory()->create(['user_id' => $seller->id, 'name' => 'ABCノート']);
        $no   = Item::factory()->create(['user_id' => $seller->id, 'name' => 'XYZペン']);

        // mylist に対象を入れておく
        $user->favorites()->create(['item_id' => $match->id]);

        // 1) おすすめタブで検索
        $res1 = $this->get(route('items.index', ['keyword' => 'ABC']));
        $res1->assertOk();

        // 2) マイリストに遷移（withQueryString で keyword が生きている想定）
        $res2 = $this->get(url('/?tab=mylist&keyword=ABC'));
        $res2->assertOk()
            ->assertSee('ABCノート')
            ->assertDontSee('XYZペン');
    }
}
