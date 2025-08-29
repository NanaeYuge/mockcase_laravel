<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
    $user = auth()->user();
    return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'postal_code' => 'required|string|max:10',
        'address' => 'required|string|max:255',
        'building' => 'nullable|string|max:255',
        'profile_image' => 'nullable|image|max:2048',
    ]);

    $user = auth()->user();

    if ($request->hasFile('profile_image')) {
        // 古い画像を削除（任意）
        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
        }

        // 新しい画像を保存
        $path = $request->file('profile_image')->store('profiles', 'public');
        $user->profile_image = $path;
    }

    $user->update($request->only('name', 'email', 'postal_code', 'address', 'building',));

    return redirect()->route('profile.edit')->with('status', 'プロフィールを更新しました。');
    }


    public function setup()
    {
    $user = auth()->user();
    $isInitial = true; // 初回設定フラグ
    return view('profile.edit', compact('user', 'isInitial'));
    }

    public function show(Request $request)
{
    $user = auth()->user();
    $tab = $request->query('tab', 'sell'); // デフォルトは 'sell'

    $items = $tab === 'buy'
        ? \App\Models\Item::whereHas('orders', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->get()
        : $user->items;

    return view('profile.show', compact('user', 'items', 'tab'));
}

    public function buyList()
    {
    $user = auth()->user();
    $buyItems = \App\Models\Item::whereHas('orders', function ($q) use ($user) {
        $q->where('user_id', $user->id);
    })->get();

    return view('profile.buy', compact('buyItems'));
    }
}
