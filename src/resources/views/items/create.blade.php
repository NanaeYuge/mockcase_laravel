@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/items/create.css') }}">

<div class="form-wrapper">
    <h2 class="form-title">商品の出品</h2>

    <form method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data">
        @csrf

    {{-- 商品画像 --}}
    <div class="form-group">
        <label>商品画像</label>

            <div class="image-drop" id="image-drop">
                <input id="image" class="visually-hidden" type="file" name="image" accept="image/*">
                    <img id="image-preview" class="image-preview" alt="" style="display:none;">
                        <button type="button" id="image-select" class="image-select-btn">画像を選択する</button>
            </div>

        <small class="help">JPG/PNG, 10MBまで</small>
    </div>

    {{-- 商品の詳細 --}}
    <h3 class="section-title">商品の詳細</h3>
    <hr class="section-divider">

    {{-- カテゴリー（タグ大量・複数行対応） --}}
    <div class="form-group">
        <label>カテゴリー</label>
        <div class="category-tags">
            @foreach ($categories as $category)
                <label class="category-tag">
                    <input type="checkbox" name="category_ids[]" value="{{ $category->id }}">
                    <span>{{ $category->name }}</span>
                </label>
        @endforeach
        </div>
    </div>

    {{-- 状態 --}}
    <div class="form-group">
        <label for="condition">商品の状態</label>
            <select name="condition" required>
                <option value="">選択してください</option>
                <option value="新品">新品</option>
                <option value="未使用に近い">未使用に近い</option>
                <option value="目立った傷や汚れなし">目立った傷や汚れなし</option>
                <option value="やや傷や汚れあり">やや傷や汚れあり</option>
                <option value="傷や汚れあり">傷や汚れあり</option>
            </select>
    </div>

    {{-- 商品名 --}}
    <div class="form-group">
        <label for="name">商品名</label>
        <input type="text" name="name" required>
    </div>

    {{-- ブランド名 --}}
    <div class="form-group">
        <label for="brand">ブランド名</label>
        <input type="text" name="brand">
    </div>

    {{-- 説明 --}}
    <div class="form-group">
        <label for="description">商品の説明</label>
        <textarea name="description" rows="4" required></textarea>
    </div>

    {{-- 価格 --}}
    <div class="form-group">
        <label for="price">販売価格</label>
            <div class="price-input">
                <span class="yen">¥</span>
                    <input type="text" name="price" id="price" inputmode="numeric" pattern="\d*" placeholder="0" value="{{ old('price') }}" autocomplete="off">
            </div>
    </div>

    <button type="submit" class="submit-button">出品する</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const drop   = document.getElementById('image-drop');
    const input  = document.getElementById('image');
    const btn    = document.getElementById('image-select');
    const prev   = document.getElementById('image-preview');

    const openPicker = () => input.click();

    const showPreview = file => {
    if (!file) return;
    const url = URL.createObjectURL(file);
    prev.src = url;
    prev.style.display = 'block';
    btn.textContent = '画像を変更する';
    drop.classList.add('has-image');
    };

    btn.addEventListener('click', openPicker);
    drop.addEventListener('click', e => {
    if (e.target === drop) openPicker();
    });

    input.addEventListener('change', e => showPreview(e.target.files[0]));

    ['dragenter','dragover'].forEach(ev => drop.addEventListener(ev, e => {
    e.preventDefault(); drop.classList.add('dragging');
    }));
    ['dragleave','drop'].forEach(ev => drop.addEventListener(ev, e => {
    e.preventDefault(); drop.classList.remove('dragging');
    }));
    drop.addEventListener('drop', e => {
    const file = e.dataTransfer.files && e.dataTransfer.files[0];
    if (file) { input.files = e.dataTransfer.files; showPreview(file); }
    });
});
</script>
@endsection
