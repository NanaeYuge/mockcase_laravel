<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(\Illuminate\Http\Request $request, $itemId)
{
    $request->validate([
        'content' => ['required','string','max:255'],
    ]);

    $item = \App\Models\Item::findOrFail($itemId);

    \App\Models\Comment::create([
        'item_id' => $item->id,
        'user_id' => auth()->id(),
        'content' => $request->input('content'),
    ]);

    $isAsync = $request->ajax()
        || $request->wantsJson()
        || $request->expectsJson()
        || $request->header('X-Requested-With') === 'XMLHttpRequest'
        || str_contains($request->header('Accept', ''), 'application/json');

    if ($isAsync) {
        return response()->json(['status' => 'ok'], 200);
    }

    return redirect()
        ->route('items.show', $item->id)
        ->with('status', 'コメントを投稿しました');
}

}
