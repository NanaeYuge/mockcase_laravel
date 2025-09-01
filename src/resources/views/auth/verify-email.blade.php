@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/auth/verify.css') }}">

<div class="verify-wrap">
    <p class="verify-msg">
    登録していただいたメールアドレスに<strong>認証メール</strong>を送付しました。<br>
    メール認証を完了してください。
    </p>

{{-- Gmail（またはメールアプリ）を開く --}}
    <a class="btn-primary" href="https://mail.google.com/mail/u/0/#inbox" target="_blank" rel="noopener">
    認証はこちらから
    </a>

{{-- 成功フラッシュ --}}
    @if (session('status') === 'verification-link-sent')
    <p class="verify-success">新しい認証メールを送信しました。受信トレイをご確認ください。</p>
    @endif

    <div class="verify-actions">
    {{-- 認証メールを再送する --}}
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="link-underline">認証メールを再送する</button>
    </form>
    </div>

    <p class="verify-note">
    ※ 迷惑メールフォルダもご確認ください。リンクが期限切れの場合は再送してください。
    </p>
</div>
@endsection
