@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
<link rel="stylesheet" href="{{ asset('css/header.css') }}">


<div class="register-wrapper">
    <div class="register-box">
        <h2 class="register-title">会員登録</h2>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <label class="register-label">ユーザー名</label>
            <input type="text" name="name" value="{{ old('name') }}" class="register-input" required>
            @error('name')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror

            <label class="register-label">メールアドレス</label>
            <input type="email" name="email" value="{{ old('email') }}" class="register-input" required>
            @error('email')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror

            <label class="register-label">パスワード</label>
            <input type="password" name="password" class="register-input" required>
            @error('password')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror

            <label class="register-label">確認用パスワード</label>
            <input type="password" name="password_confirmation" class="register-input" required>

            <button type="submit" class="register-button">登録する</button>

            <div class="register-link">
                <a href="{{ route('login') }}">ログインはこちら</a>
            </div>
        </form>
    </div>
</div>

@if (session('status') === 'verification-link-sent')
    <div class="verification-box">
        <p class="verify-message">
            登録していただいたメールアドレスに認証メールを送付しました。<br>
            メール認証を完了してください。
        </p>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="verify-button">認証はこちらから</button>
        </form>

        <div class="verify-link">
            <a href="{{ route('verification.resend') }}">認証メールを再送する</a>
        </div>
    </div>
@endif

@endsection
