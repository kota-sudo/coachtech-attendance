@extends('layouts.app')

@section('title', 'CSV出力')

@section('header-actions')
    <a href="{{ route('admin.attendance.staff.show', ['user' => $user->id, 'month' => $month]) }}" class="nav-link">月次勤怠に戻る</a>
@endsection

@section('content')
    <div class="list-card">
        <p>CSV出力機能は次のステップで実装予定です。</p>
    </div>
@endsection
