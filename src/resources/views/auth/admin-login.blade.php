@extends('layouts.guest')

@section('title', '管理者ログイン')

@section('content')
    <h1>管理者ログイン</h1>
    <form method="POST" action="{{ url('/admin/login') }}">
        @csrf
        <label for="email">メールアドレス</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
        @error('email')<p class="error">{{ $message }}</p>@enderror

        <label for="password">パスワード</label>
        <input id="password" type="password" name="password" required>
        @error('password')<p class="error">{{ $message }}</p>@enderror

        <button type="submit">ログインする</button>
    </form>
@endsection
