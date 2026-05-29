@extends('layouts.app')

@php
    use App\Enums\AttendanceStatus;
@endphp

@section('title', '勤怠')

@section('header-actions')
    <a href="{{ route('attendance.list') }}" class="nav-link">勤怠一覧</a>
    <form class="inline" method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="link">ログアウト</button>
    </form>
@endsection

@section('content')
    <div class="attendance-card">
        <p class="attendance-date">{{ $todayLabel }}</p>
        <p class="attendance-clock" id="current-time" aria-live="polite"></p>

        <div class="attendance-status">
            <span class="attendance-status-label">勤務ステータス</span>
            <span class="attendance-status-value">{{ $status->label() }}</span>
        </div>

        <div class="attendance-actions">
            @if ($status === AttendanceStatus::OffDuty)
                <form method="POST" action="{{ route('attendance.clock-in') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="width:100%">出勤</button>
                </form>
            @elseif ($status === AttendanceStatus::Working)
                <form method="POST" action="{{ route('attendance.break-in') }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary" style="width:100%">休憩入</button>
                </form>
                <form method="POST" action="{{ route('attendance.clock-out') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="width:100%">退勤</button>
                </form>
            @elseif ($status === AttendanceStatus::Breaking)
                <form method="POST" action="{{ route('attendance.break-out') }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary" style="width:100%">休憩戻</button>
                </form>
            @else
                <p class="attendance-message">お疲れ様でした。</p>
            @endif
        </div>
    </div>

    <script>
        (function () {
            const el = document.getElementById('current-time');
            const formatter = new Intl.DateTimeFormat('ja-JP', {
                timeZone: 'Asia/Tokyo',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false,
            });

            function tick() {
                el.textContent = formatter.format(new Date());
            }

            tick();
            setInterval(tick, 1000);
        })();
    </script>
@endsection
