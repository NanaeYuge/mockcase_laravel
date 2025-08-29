<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_post_comment()
    {
        $item = Item::factory()->create();
        $res = $this->post(route('comments.store', $item->id), ['content' => 'NG?']);
        $res->assertRedirect('/login'); // スキャフォールドに合わせて
        $this->assertDatabaseCount('comments', 0);
    }

    public function test_comment_required_validation()
    {
        $user = User::factory()->create();
        $this->be($user);
        $item = Item::factory()->create();

        $res = $this->post(route('comments.store', $item->id), ['content' => '']);
        $res->assertSessionHasErrors(['content']);
    }

    public function test_comment_max_255_validation()
    {
        $user = User::factory()->create();
        $this->be($user);
        $item = Item::factory()->create();

        $res = $this->post(route('comments.store', $item->id), ['content' => str_repeat('あ', 256)]);
        $res->assertSessionHasErrors(['content']);
    }

    public function test_comment_success()
    {
        $user = User::factory()->create();
        $this->be($user);
        $item = Item::factory()->create();

        $res = $this->post(route('comments.store', $item->id), ['content' => '買いたいです']);
        $res->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'content' => '買いたいです',
        ]);
    }
}
