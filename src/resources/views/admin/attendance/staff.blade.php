@extends('layouts.app')

@section('title', '月次勤怠（管理者）')

@section('header-actions')
    <a href="{{ route('admin.staff.list') }}" class="nav-link">スタッフ一覧に戻る</a>
    <form class="inline" method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit" class="link">ログアウト</button>
    </form>
@endsection

@section('content')
    <div class="list-card">
        <p>スタッフ月次勤怠画面（実装予定）</p>
        <p>{{ $user->name }}（{{ $user->email }}）</p>
    </div>
@endsection
