@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
<link rel="stylesheet" href="{{ asset('css/header.css') }}">

<div class="login-wrapper">
    <div class="login-box">
        <h2 class="login-title">ログイン</h2>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <label class="login-label">メールアドレス</label>
            <input type="email" name="email" value="{{ old('email') }}" class="login-input" required>
            @error('email')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror

            <label class="login-label">パスワード</label>
            <input type="password" name="password" class="login-input" required>
            @error('password')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror

            <button type="submit" class="login-button">ログイン</button>

            <div class="login-link">
                <a href="{{ route('register') }}">新規登録はこちら</a>
            </div>
        </form>
    </div>
</div>
@endsection
