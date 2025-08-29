<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;

class PurchasePageContentTest extends TestCase
{
    use RefreshDatabase;

    /** ログイン時に購入画面の主要な文言が表示されること */
    public function test_purchase_page_displays_key_content_for_logged_in_user()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $res = $this->get(route('purchase.create', ['id' => $item->id]));

        $res->assertOk()
        ->assertSee('支払い方法')
        ->assertSee('配送先')     // ← '住所' ではなく '配送先'
        ->assertSee('購入する')   // ← '購入手続き' ではなく ボタン文言
        ->assertSee($item->name);

    }

    /** ゲストは購入画面にアクセスできず、ログインへリダイレクトされること */
    public function test_guest_is_redirected_to_login_when_accessing_purchase_page()
    {
        $item = Item::factory()->create();

        $res = $this->get(route('purchase.create', ['id' => $item->id]));

        $res->assertRedirect('/login');
    }
}
