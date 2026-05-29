@extends('layouts.app')

@section('title', '勤怠詳細（管理者）')

@section('header-actions')
    <a href="{{ route('admin.attendance.list', ['date' => $attendance->work_date->format('Y-m-d')]) }}" class="nav-link">一覧に戻る</a>
    <form class="inline" method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit" class="link">ログアウト</button>
    </form>
@endsection

@section('content')
    <div class="list-card">
        <p>管理者用勤怠詳細画面（実装予定）</p>
        <p>{{ $attendance->user->name }} — {{ $attendance->work_date->locale('ja')->isoFormat('YYYY年M月D日(ddd)') }}</p>
    </div>
@endsection
