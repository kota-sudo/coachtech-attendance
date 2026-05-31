@extends('layouts.guest')

@section('title', 'メール認証')

@section('content')
    <h1>メール認証</h1>

    @if (session('status'))
        <p class="flash-status">{{ session('status') }}</p>
    @endif

    <p class="verify-message">
        登録していただいたメールアドレスに認証メールを送付しました。<br>
        メール認証を完了してください。
    </p>

    <div class="verify-actions">
        <a href="{{ $verificationUrl }}" class="btn-verify-link">認証はこちらから</a>

        <form method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <button type="submit" class="btn-resend">認証メール再送</button>
        </form>
    </div>
@endsection
