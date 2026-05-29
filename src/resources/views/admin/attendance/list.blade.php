@extends('layouts.app')

@section('title', '勤怠一覧（管理者）')

@section('header-actions')
    <form class="inline" method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit" class="link">ログアウト</button>
    </form>
@endsection

@section('content')
    <p>管理者用勤怠一覧画面（実装予定）</p>
    <p>ログイン中: {{ auth()->user()->name }}（管理者）</p>
@endsection
