<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;

class SoldLabelTest extends TestCase
{
    use RefreshDatabase;

    /** 一覧で購入済み商品に Sold が表示される */
    public function test_index_shows_sold_label_for_purchased_items()
    {
        $seller = User::factory()->create();
        $buyer  = User::factory()->create([
            'postal_code' => '123-4567',
            'address' => 'テスト区テスト町1-1',
            'building' => 'ビル101',
        ]);

        $sold   = Item::factory()->create(['user_id' => $seller->id, 'name' => '売れた品']);
        $unsold = Item::factory()->create(['user_id' => $seller->id, 'name' => '未売品']);

        $this->actingAs($buyer)->post(url("/purchase/{$sold->id}"), [
            'payment_method' => 'クレジットカード',
        ])->assertRedirect();

        $this->assertDatabaseHas('orders', ['item_id' => $sold->id]);

        $res = $this->get(route('items.index'));
        $res->assertOk()
            ->assertSee('売れた品')
            ->assertSee('未売品')
            ->assertSee('Sold');
    }

    public function test_mylist_shows_sold_label_too()
{
    $seller = User::factory()->create();
    $buyer  = User::factory()->create([
        'postal_code' => '123-4567',
        'address' => 'テスト区テスト町1-1',
        'building' => 'ビル101',
    ]);

    $item = Item::factory()->create(['user_id' => $seller->id, 'name' => 'マイリスト売れた品']);

    $buyer->favorites()->create(['item_id' => $item->id]);

    $this->actingAs($buyer)->post(url("/purchase/{$item->id}"), [
        'payment_method' => 'クレジットカード',
    ])->assertRedirect();

    $this->assertDatabaseHas('orders', ['item_id' => $item->id]);

    $res = $this->actingAs($buyer)->get(route('items.index', ['tab' => 'mylist']));
    $res->assertOk()
        ->assertSee('マイリスト売れた品')
        ->assertSee('Sold');
}
}
