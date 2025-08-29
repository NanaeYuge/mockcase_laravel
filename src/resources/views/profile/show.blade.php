@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/profile/show.css') }}?v={{ time() }}">

@php
  $tab = request('tab') === 'buy' ? 'buy' : 'sell'; // デフォルトは出品した商品
@endphp

<div class="profile-wrapper">

{{-- ヘッダー --}}
    <div class="profile-header">
    <img
      src="{{ Auth::user()->profile_image ? Storage::url(Auth::user()->profile_image) : asset('images/default-user.png') }}"
      alt="プロフィール画像"
      class="profile-image">
    <div class="profile-info">
      <h2 class="user-name">{{ Auth::user()->name }}</h2>
      <a href="{{ route('profile.edit') }}" class="btn-edit">プロフィールを編集</a>
    </div>
  </div>

  {{-- タブ --}}
  <nav class="tab-menu">
    <a href="?tab=sell" class="tab {{ $tab === 'sell' ? 'active' : '' }}">出品した商品</a>
    <a href="?tab=buy"  class="tab {{ $tab === 'buy'  ? 'active' : '' }}">購入した商品</a>
  </nav>

  {{-- 商品グリッド --}}
  <div class="item-grid">
    @forelse ($items as $item)
      <a href="{{ route('items.show', $item->id) }}" class="item-card">
        <div class="thumb">
          @if(!empty($item->image_url))
            <img src="{{ $item->image_url }}" alt="{{ $item->name }}">
          @elseif(!empty($item->image_path))
            <img src="{{ Storage::url($item->image_path) }}" alt="{{ $item->name }}">
          @else
            <div class="no-image">商品画像</div>
          @endif
        </div>
        <div class="meta">
          <p class="item-name">{{ $item->name }}</p>
          <p class="item-price">¥{{ number_format($item->price) }}</p>
        </div>
      </a>
    @empty
      <p class="no-items">表示する商品がありません。</p>
    @endforelse
  </div>

  {{-- ページネーション（あれば） --}}
  @if(method_exists($items, 'links'))
    <div class="pager">
      {{ $items->withQueryString()->links() }}
    </div>
  @endif

</div>
@endsection
