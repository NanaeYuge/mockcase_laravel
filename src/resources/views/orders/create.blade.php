@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/orders/create.css') }}">

@php
    $current = old('payment_method', $selected ?? null);
@endphp

<form id="purchaseForm" method="POST"
        action="{{ $current==='クレジットカード' ? route('purchase.checkout', $item->id) : route('purchase.store', $item->id) }}">
    @csrf

<div class="purchase-wrapper">
    {{-- 左カラム --}}
    <div class="purchase-left">
        {{-- 商品ヘッダ --}}
        <div class="item-header">
            <div class="item-image-block">
            <div class="caption">商品画像</div>
            <div class="item-image-box">
            <img src="{{ $item->image_url }}" alt="商品画像" class="item-image">
            </div>
        </div>
        <div class="item-info">
            <h2 class="item-title">{{ $item->name }}</h2>
            <p class="price">¥{{ number_format($item->price) }} <span class="tax">（税込）</span></p>
        </div>
        </div>

        <hr class="separator">

        {{-- 支払い方法 --}}
        <div class="section payment-section">
        <div class="section-header">
            <h3>支払い方法</h3>
        </div>

        <label for="payment_method" class="sr-only">支払い方法</label>
        <select id="payment_method" name="payment_method" class="form-control" required>
            <option value="" disabled {{ $current ? '' : 'selected' }}>支払い方法を選択</option>
            {{-- Stripeでカード決済（Checkoutへ遷移） --}}
            <option value="クレジットカード" {{ $current==='クレジットカード' ? 'selected' : '' }}>
            クレジットカード
            </option>
            <option value="コンビニ払い" {{ $current==='コンビニ払い' ? 'selected' : '' }}>コンビニ払い</option>
        </select>

        @error('payment_method')
            <div class="form-error" role="alert">{{ $message }}</div>
        @enderror
        </div>

        {{-- 仕切り線（支払方法と配送先の間） --}}
        <hr class="separator">

        {{-- 配送先 --}}
        <div class="section address-section">
        <div class="section-header">
            <h3>配送先</h3>
            {{-- 住所変更リンク（デザイン通りリンク風） --}}
            <a href="{{ route('purchase.address.edit', $item->id) }}" class="edit-link">住所を変更する</a>
        </div>

        @auth
            <p class="address-text">
            〒{{ Auth::user()->postal_code }}<br>
            {{ Auth::user()->address }} {{ Auth::user()->building }}
            </p>
        @else
            <p>ログインしてください。</p>
        @endauth
        </div>

        {{-- SP用のサマリ（PCでは非表示） --}}
        <div class="summary-box mobile-only">
        <p>商品代金：<strong>¥{{ number_format($item->price) }}</strong></p>
        <p>支払い方法：<span id="selected-method-mobile">{{ $current ?? '未選択' }}</span></p>
        </div>
    </div>

    {{-- 右カラム（枠で囲む） --}}
    <aside class="purchase-right desktop-only">
        <div class="summary-card">
        <div class="summary-row">
            <span>商品代金</span>
            <span>¥{{ number_format($item->price) }}</span>
        </div>
        <div class="summary-row">
            <span>支払い方法</span>
            <span id="selected-method">{{ $current ?? '未選択' }}</span>
        </div>
        </div>

        {{-- 赤い枠ボタン --}}
        <button type="submit" class="purchase-btn" aria-label="購入する">購入する</button>
    </aside>
    </div>

    {{-- 必要に応じて商品ID等のHiddenを追加してOK --}}
    <input type="hidden" name="item_id" value="{{ $item->id }}">
</form>

{{-- 選択肢 → 右サマリ／SPサマリへ反映 + フォーム送信先を切り替え --}}
<script>
    (function() {
    const form = document.getElementById('purchaseForm');
    const sel  = document.getElementById('payment_method');
    const outPc = document.getElementById('selected-method');
    const outSp = document.getElementById('selected-method-mobile');

    function sync() {
        const v = sel.value || '未選択';
        if (outPc) outPc.textContent = v;
        if (outSp) outSp.textContent = v;


        const checkoutUrl = @json(route('purchase.checkout', $item->id));
        const storeUrl    = @json(route('purchase.store',   $item->id));
        form.action = (v === 'クレジットカード') ? checkoutUrl : storeUrl;
    }

    sel.addEventListener('change', sync);
    sync();
    })();
</script>
@endsection
