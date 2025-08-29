<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodSelectionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_purchase_item_with_credit_card()
    {
        $buyer  = User::factory()->create();
        $seller = User::factory()->create();
        $item   = Item::factory()->create(['user_id' => $seller->id]);

        $res = $this->actingAs($buyer)->post(url("/purchase/{$item->id}"), [
            'payment_method' => 'クレジットカード',
        ]);

        // 注文が作成され、一覧へリダイレクト（実装に合わせて）
        $res->assertRedirect(route('items.index'));

        $this->assertDatabaseHas('orders', [
            'user_id'        => $buyer->id,
            'item_id'        => $item->id,
            'payment_method' => 'クレジットカード',
        ]);
    }

    /** @test */
    public function invalid_payment_method_will_be_rejected()
    {
        $buyer  = User::factory()->create();
        $seller = User::factory()->create();
        $item   = Item::factory()->create(['user_id' => $seller->id]);

        $res = $this->actingAs($buyer)->from(url("/purchase/{$item->id}"))
            ->post(url("/purchase/{$item->id}"), [
                'payment_method' => 'ビットコイン', // バリデーション外
            ]);

        $res->assertRedirect(url("/purchase/{$item->id}"));
        $res->assertSessionHasErrors('payment_method');

        $this->assertDatabaseMissing('orders', [
            'item_id' => $item->id,
        ]);
    }
}
