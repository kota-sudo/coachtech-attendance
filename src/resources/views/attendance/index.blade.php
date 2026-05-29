@extends('layouts.app')

@section('title', '勤怠')

@section('header-actions')
    <form class="inline" method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="link">ログアウト</button>
    </form>
@endsection

@section('content')
    <p>勤怠打刻画面（実装予定）</p>
    <p>ログイン中: {{ auth()->user()->name }}（一般ユーザー）</p>
@endsection
