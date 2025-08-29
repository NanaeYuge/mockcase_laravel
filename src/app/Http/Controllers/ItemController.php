<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ItemController extends Controller
{
    /** 出品画面 */
    public function create()
    {
        $categories = Category::all();
        return view('items.create', compact('categories'));
    }

    /** 保存 */
    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'price'        => 'required|integer|min:0',
            'condition'    => 'required|string|in:新品,未使用に近い,中古,良好',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'image'        => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $original = $request->file('image')->getClientOriginalName();
            $safeName = time() . '_' . Str::slug(pathinfo($original, PATHINFO_FILENAME))
                        . '.' . $request->file('image')->getClientOriginalExtension();
            $imagePath = $request->file('image')->storeAs('items', $safeName, 'public');
        }

        $item = Item::create([
            'user_id'     => auth()->id(),
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'condition'   => $request->condition,
            'image_path'  => $imagePath,
        ]);

        $item->categories()->attach($request->category_ids);

        return redirect()->route('items.index')->with('status', '商品を出品しました！');
    }

    public function index(Request $request)
    {
    $tab = $request->query('tab', 'recommend');
    $keyword = trim((string) $request->query('keyword', ''));

    $query = Item::query()
    ->withCount(['favorites', 'comments', 'orders'])
    ->orderByDesc('id');

    if ($keyword !== '') {
        $query->where('name', 'like', "%{$keyword}%");
    }

    if ($tab === 'mylist' && auth()->check()) {
        $query->whereHas('favorites', function ($q) {
            $q->where('user_id', auth()->id());
        });
    }

    $items = $query->paginate(7)->withQueryString();

    return view('items.index', compact('items', 'tab', 'keyword'));
}


    /** 詳細 */
    public function show($id)
    {
        $item = Item::with([
                    'user',
                    'comments.user',
                    'categories',
                ])
                ->withCount(['favorites','comments'])
                ->findOrFail($id);

        return view('items.show', compact('item'));
    }

    /** 更新 */
    public function update(Request $request, Item $item)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'price'        => 'required|integer|min:0',
            'condition'    => 'required|string|in:新品,未使用に近い,中古,良好',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'image'        => 'nullable|image|max:2048',
        ]);

        // 画像削除
        if ($request->has('delete_image') && $item->image_path) {
            Storage::disk('public')->delete($item->image_path);
            $item->image_path = null;
        }

        // 新規アップロード
        if ($request->hasFile('image')) {
            if ($item->image_path) {
                Storage::disk('public')->delete($item->image_path);
            }
            $original = $request->file('image')->getClientOriginalName();
            $safeName = time() . '_' . Str::slug(pathinfo($original, PATHINFO_FILENAME))
                        . '.' . $request->file('image')->getClientOriginalExtension();
            $item->image_path = $request->file('image')->storeAs('items', $safeName, 'public');
        }

        // フィールド更新
        $item->update([
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'condition'   => $request->condition,
        ]);

        // カテゴリ更新
        $item->categories()->sync($request->category_ids);

        return redirect()->route('items.show', $item->id)->with('status', '商品を更新しました。');
    }
}
