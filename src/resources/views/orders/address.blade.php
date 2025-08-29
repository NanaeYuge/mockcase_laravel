@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/orders/address.css') }}">

<div class="address-wrapper">
    <h2 class="address-title">住所の変更</h2>

    <form action="{{ route('purchase.address.update', $item->id) }}" method="POST" class="address-form">
        @csrf

        <label>郵便番号</label>
        <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}" required>

        <label>住所</label>
        <input type="text" name="address" value="{{ old('address', $user->address) }}" required>

        <label>建物名（任意）</label>
        <input type="text" name="building" value="{{ old('building', $user->building) }}">

        <button type="submit" class="btn-update">更新して購入手続きに戻る</button>
    </form>
</div>
@endsection
