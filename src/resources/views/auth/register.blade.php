@extends('layouts.guest')

@section('title', '会員登録')

@section('content')
    <h1>会員登録</h1>
    <form method="POST" action="{{ url('/register') }}">
        @csrf
        <label for="name">お名前</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
        @error('name')<p class="error">{{ $message }}</p>@enderror

        <label for="email">メールアドレス</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required>
        @error('email')<p class="error">{{ $message }}</p>@enderror

        <label for="password">パスワード</label>
        <input id="password" type="password" name="password" required>
        @error('password')<p class="error">{{ $message }}</p>@enderror

        <label for="password_confirmation">パスワード確認</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required>

        <button type="submit">登録する</button>
    </form>
    <p class="links"><a href="{{ route('login') }}">ログインはこちら</a></p>
@endsection
