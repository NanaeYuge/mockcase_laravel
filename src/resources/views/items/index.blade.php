@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/items/index.css') }}">
<link rel="stylesheet" href="{{ asset('css/header.css') }}">

<div class="items-wrapper">
    <div class="items-container">
        <h2 class="items-title">商品一覧</h2>

        {{-- タブ（検索キーワードだけ引き継ぐ） --}}
        @php
            $qs = request()->filled('keyword') ? ['keyword' => request('keyword')] : [];
        @endphp

        <div class="tab">
            <a href="{{ route('items.index', $qs) }}"
                class="{{ ($tab ?? null) !== 'mylist' ? 'active' : '' }}">おすすめ</a>
            <a href="{{ route('items.index', array_merge($qs, ['tab' => 'mylist'])) }}"
                class="{{ ($tab ?? null) === 'mylist' ? 'active' : '' }}">マイリスト</a>
        </div>

        <div class="item-list">
            @foreach($items as $item)
                <a href="{{ route('items.show', $item->id) }}" class="item-card">
                    <div class="image-wrap">
                        @php
                            $path = $item->image_path;
                            if (!$path) {
                                $url = asset('images/no-image.png');
                            }

                            elseif (preg_match('~^https?://~', $path)) {
                                $url = $path;
                            }
                            elseif (str_starts_with($path, 'storage/')) {
                                $url = '/'.$path;
                            }
                            elseif (str_starts_with($path, 'public/')) {
                                $url = '/storage/'.substr($path, 7);
                            }
                            else {
                                $url = Storage::disk('public')->url($path);
                            }
                        @endphp

                        <img class="item-image" src="{{ $url }}" alt="{{ $item->name }}"
                            onerror="this.onerror=null;this.src='{{ asset('images/no-image.png') }}';">

                        @if($item->orders_count > 0)
                            <span class="sold-label">Sold</span>
                        @endif
                    </div>
                    <p class="item-name">{{ $item->name }}</p>
                    <p class="item-price">¥{{ number_format($item->price) }}</p>
                </a>
            @endforeach
        </div>

        <div class="pagination">
            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection
