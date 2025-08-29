<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;

class PurchasePostValidationTest extends TestCase
{
    use RefreshDatabase;

    /** 未選択だとバリデーションエラーになる（login必須） */
    public function test_payment_method_is_required()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $this->actingAs($user);

        $res = $this->from(route('purchase.create', ['id' => $item->id]))
            ->post(route('purchase.create', ['id' => $item->id]), [
                'payment_method' => '', // 未選択
            ]);

        $res->assertRedirect(route('purchase.create', ['id' => $item->id]));
        $res->assertInvalid(['payment_method']);
    }

    /** 正常系：選択されていれば処理成功（実装に合わせてリダイレクト/200を調整） */
    public function test_purchase_post_success_with_valid_payment_method()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $this->actingAs($user);

        $res = $this->post(route('purchase.create', ['id' => $item->id]), [
            'payment_method' => 'クレジットカード',
        ]);

        // 実装がリダイレクトなら：
        $res->assertRedirect();
    }
}
