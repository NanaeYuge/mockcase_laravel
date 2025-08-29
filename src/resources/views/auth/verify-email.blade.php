@extends('layouts.app')

@section('content')
<div class="verify-wrapper">
    <p class="verify-message">
        登録していただいたメールアドレスに認証メールを送付しました。<br>
        メール認証を完了してください。
    </p>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="verify-button">認証はこちらから</button>
    </form>

    <div class="verify-link">
        <a href="{{ route('verification.send') }}">認証メールを再送する</a>
    </div>
</div>
@endsection
