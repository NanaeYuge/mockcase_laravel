<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_update_address_and_return_to_purchase_page()
    {
        $user = User::factory()->create([
            'postal_code' => '000-0000',
            'address'     => '旧住所',
            'building'    => '旧ビル',
        ]);
        $item = Item::factory()->create();

        $res = $this->actingAs($user)->post(route('purchase.address.update', $item->id), [
            'postal_code' => '123-4567',
            'address'     => '東京都渋谷区1-2-3',
            'building'    => '渋谷ビル501',
        ]);

        // 実装で購入画面へ戻す場合
        $res->assertRedirect(route('purchase.create', $item->id));

        $this->assertDatabaseHas('users', [
            'id'          => $user->id,
            'postal_code' => '123-4567',
            'address'     => '東京都渋谷区1-2-3',
            'building'    => '渋谷ビル501',
        ]);
    }

    /** @test */
    public function address_update_requires_postal_code_and_address()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $res = $this->actingAs($user)
            ->from(route('purchase.address.edit', $item->id))
            ->post(route('purchase.address.update', $item->id), [
                'postal_code' => '',    // 必須
                'address'     => '',    // 必須
                'building'    => null,  // 任意
            ]);

        $res->assertRedirect(route('purchase.address.edit', $item->id));
        $res->assertSessionHasErrors(['postal_code', 'address']);
    }
}
