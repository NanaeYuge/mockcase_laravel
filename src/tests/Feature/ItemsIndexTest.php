<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Item;
use App\Models\User;

class ItemsIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_items_index()
    {
        $item = Item::factory()->create(['name' => '表示テスト商品']);
        $res = $this->get(route('items.index'));

        $res->assertOk()
            ->assertSee('表示テスト商品');
    }

    public function test_guest_does_not_see_favorite_button_on_index()
    {
        Item::factory()->count(2)->create();

        $res = $this->get(route('items.index'));
        $res->assertOk()
            ->assertDontSee('いいね')
            ->assertDontSee('favorite');
    }

    public function test_index_paginates_seven_per_page()
    {
    foreach (range(1, 8) as $i) {
        Item::factory()->create(['name' => "ITEM{$i}"]);
    }

    $p1 = $this->get(route('items.index', ['page' => 1]));
    $p1->assertOk();

    $html1 = $p1->getContent();
    $this->assertSame(7, substr_count($html1, 'class="item-card"'));

    $absentOnPage1 = str_contains($html1, 'ITEM8') ? 'ITEM1' : 'ITEM8';

    $p2 = $this->get(route('items.index', ['page' => 2]));
    $p2->assertOk()
        ->assertSee($absentOnPage1);

    $p1->assertDontSee($absentOnPage1);
    }


    public function test_logged_in_user_sees_index_normally()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create(['name' => 'ログイン時も見える']);
        $res = $this->get(route('items.index'));

        $res->assertOk()
            ->assertSee('ログイン時も見える');
    }
}
