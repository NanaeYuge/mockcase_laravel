<header class="main-header">
    <div class="header-inner">

{{-- ロゴ --}}
    <div class="logo">
        <a href="{{ route('items.index') }}">
        <img src="{{ asset('images/coachtech-logo.svg') }}" alt="COACHTECH" class="logo-image">
        </a>
    </div>

{{-- 検索 --}}
    <form class="search-form" method="GET"  action="{{ route('items.index') }}">
        <input type="text" class="header-search" name="keyword" value="{{ request('keyword') }}" placeholder="なにをお探しですか？">
        <button type="submit" class="btn-red">検索</button>
    </form>

{{-- 右側ナビ --}}
    <nav class="header-nav">
        @auth
        {{-- ログイン中 --}}
        <a href="{{ route('mypage') }}">マイページ</a>

        {{-- ログアウト（POST送信） --}}
        <a href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            ログアウト
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
            @csrf
        </form>

        <a href="{{ route('items.create') }}" class="btn-outline">出品</a>
        @else
        {{-- ログアウト中（Figma要件：ログイン/マイページ/出品 すべて表示、遷移先はログイン） --}}
        <a href="{{ route('login') }}">ログイン</a>
        <a href="{{ route('login') }}">マイページ</a>
        <a href="{{ route('login') }}" class="btn-outline">出品</a>
        @endauth
    </nav>

    </div>
</header>
