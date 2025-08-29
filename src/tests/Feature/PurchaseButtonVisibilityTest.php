<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Item;
use App\Models\User;

class PurchaseButtonVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_button_but_redirects_to_login_when_click()
    {
        $item = Item::factory()->create(['name' => 'X']);

        // 詳細ページでボタンが見える（仕様に合わせる）
        $res = $this->get(route('items.show', ['id' => $item->id]));
        $res->assertOk()
            ->assertSee('X')
            ->assertSee('購入手続きへ'); // ←ここをassertSeeに変更

        // 実際にクリック相当（/purchase/{id} へアクセス）でログインへ飛ぶ
        $go = $this->get("/purchase/{$item->id}");
        // Fortify でデフォルトは /login へ
        $go->assertRedirect('/login');
    }

    public function test_logged_in_user_can_see_and_access_purchase_page()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $res = $this->get(route('items.show', ['id' => $item->id]));
        $res->assertOk()
            ->assertSee('購入手続きへ');

        // ログイン時は購入ページにアクセスできる（画面が200で開く想定）
        $page = $this->get("/purchase/{$item->id}");
        $page->assertOk(); // create画面の見出し文言があれば assertSee にしてもOK
        // 例: $page->assertSee('支払い方法'); など実装に合わせて
    }
}
