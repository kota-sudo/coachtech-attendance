@extends('layouts.guest')

@section('title', 'ログイン')

@section('content')
    <h1>ログイン</h1>
    <form method="POST" action="{{ url('/login') }}">
        @csrf
        <label for="email">メールアドレス</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
        @error('email')<p class="error">{{ $message }}</p>@enderror

        <label for="password">パスワード</label>
        <input id="password" type="password" name="password" required>
        @error('password')<p class="error">{{ $message }}</p>@enderror

        <button type="submit">ログインする</button>
    </form>
    <p class="links">
        <a href="{{ route('register') }}">会員登録はこちら</a>
    </p>
@endsection
