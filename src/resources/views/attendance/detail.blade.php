@extends('layouts.app')

@section('title', '勤怠詳細')

@section('header-actions')
    <a href="{{ route('attendance.list') }}" class="nav-link">一覧に戻る</a>
    <form class="inline" method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="link">ログアウト</button>
    </form>
@endsection

@section('content')
    <div class="list-card">
        <p>勤怠詳細画面（実装予定）</p>
        <p>日付: {{ $attendance->work_date->locale('ja')->isoFormat('YYYY年M月D日(ddd)') }}</p>
    </div>
@endsection
