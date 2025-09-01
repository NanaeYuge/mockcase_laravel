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


    public function store(Request $request)
    {
        $validated = $request->validate([
            'image'          => 'nullable|image|max:10240',
            'name'           => 'required|string|max:255',
            'brand'          => 'nullable|string|max:255',
            'description'    => 'required|string',
            'price'          => 'required|integer|min:0',
            'condition'      => 'required|string|in:新品,未使用に近い,目立った傷や汚れなし,やや傷や汚れあり,傷や汚れあり',
            'category_ids'   => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
    ]);

        $item = new Item();
        $item->fill([
            'name'        => $validated['name'],
            'brand'       => $validated['brand'] ?? null,
            'description' => $validated['description'],
            'price'       => $validated['price'],
            'condition'   => $validated['condition'],
            'user_id'     => auth()->id(),
    ]);

    if ($request->hasFile('image')) {
        $original = $request->file('image')->getClientOriginalName();
        $safeName = time().'_'.Str::slug(pathinfo($original, PATHINFO_FILENAME))
                    .'.'.$request->file('image')->getClientOriginalExtension();

        $item->image_path = $request->file('image')->storeAs('items', $safeName, 'public');
    }

    $item->save();


    $item->categories()->attach($validated['category_ids']);

    return redirect()->route('items.show', $item->id)->with('status', '商品を出品しました。');
}

public function update(Request $request, Item $item)
{
    $validated = $request->validate([
        'name'           => 'required|string|max:255',
        'description'    => 'nullable|string',
        'price'          => 'required|integer|min:0',
        'condition'      => 'required|string|in:新品,未使用に近い,目立った傷や汚れなし,やや傷や汚れあり,傷や汚れあり',
        'category_ids'   => 'required|array|min:1',
        'category_ids.*' => 'exists:categories,id',
        'image'          => 'nullable|image|max:10240',
        'delete_image'   => 'nullable',
    ]);

    // 画像削除
    if ($request->boolean('delete_image') && $item->image_path) {
        Storage::disk('public')->delete($item->image_path);
        $item->image_path = null;
    }

    // 新規アップロード
    if ($request->hasFile('image')) {
        if ($item->image_path) {
            Storage::disk('public')->delete($item->image_path);
        }
        $original = $request->file('image')->getClientOriginalName();
        $safeName = time().'_'.Str::slug(pathinfo($original, PATHINFO_FILENAME))
                    .'.'.$request->file('image')->getClientOriginalExtension();

        $item->image_path = $request->file('image')->storeAs('items', $safeName, 'public');
    }

    $item->fill([
        'name'        => $validated['name'],
        'description' => $validated['description'] ?? null,
        'price'       => $validated['price'],
        'condition'   => $validated['condition'],
    ]);
    $item->save();

    $item->categories()->sync($validated['category_ids']);

    return redirect()->route('items.show', $item->id)->with('status', '商品を更新しました。');
}


}
