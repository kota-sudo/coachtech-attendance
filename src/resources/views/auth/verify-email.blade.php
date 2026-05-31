@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/user-login.css') }}">
@endsection

@section('content')
<div class="verify-email__content">
    <h2 class="verify-email__heading">メール認証</h2>
    @if (session('status'))
        <p class="flash-status">{{ session('status') }}</p>
    @endif
    <p class="verify-email__message">
        登録していただいたメールアドレスに認証メールを送付しました。<br>
        メール認証を完了してください。
    </p>
    <a href="{{ $verificationUrl }}" class="verify-email__button">認証はこちらから</a>
    <form method="POST" action="{{ route('verification.resend') }}" style="display:inline;">
        @csrf
        <button type="submit" class="verify-email__resend">認証メール再送</button>
    </form>
</div>
@endsection
