<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;      // ← 追加
use App\Models\Favorite;  // ← 追加

class FavoriteController extends Controller
{
    /** お気に入り登録 */
    public function store(Item $item, Request $request)
    {
        Favorite::firstOrCreate([
            'user_id' => $request->user()->id,
            'item_id' => $item->id,
        ]);

        // 非同期(AJAX)ならJSON、通常POSTなら直前に戻る
        if ($request->wantsJson()) {
            return response()->json([
                'liked' => true,
                'count' => $item->favorites()->count(),
            ]);
        }

        return back();
    }

    /** お気に入り解除 */
    public function destroy(Item $item, Request $request)
    {
        Favorite::where('user_id', $request->user()->id)
            ->where('item_id', $item->id)
            ->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'liked' => false,
                'count' => $item->favorites()->count(),
            ]);
        }

        return back();
    }
}
