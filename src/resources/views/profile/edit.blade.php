@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/profile/edit.css') }}">

<div class="profile-edit-wrapper">

    <!-- タイトル中央 -->
    <h2 class="profile-title">プロフィール設定</h2>

    @if (session('status'))
        <p class="status-message">{{ session('status') }}</p>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- グレーの丸 と 画像選択ボタン 横並び -->
        <div class="profile-image-area">
            <div class="profile-image-wrapper">
                @if ($user->profile_image)
                    <img src="{{ asset('storage/' . $user->profile_image) }}" alt="プロフィール画像" class="profile-image">
                @else
                    <div class="profile-placeholder"></div>
                @endif
            </div>

            <label class="btn-select-image">
                画像を選択する
                <input type="file" name="profile_image" hidden>
            </label>
        </div>

        @error('profile_image') 
            <p class="error">{{ $message }}</p> 
        @enderror

        <div class="form-group">
            <label>ユーザー名</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}">
            @error('name') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label>メールアドレス</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}">
            @error('email') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label>郵便番号</label>
            <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}">
            @error('postal_code') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label>住所</label>
            <input type="text" name="address" value="{{ old('address', $user->address) }}">
            @error('address') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label>建物名</label>
            <input type="text" name="building" value="{{ old('building', $user->building) }}">
            @error('building') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div style="text-align: center;">
            <button type="submit" class="btn-red-update">更新する</button>
        </div>
    </form>
</div>
@endsection
