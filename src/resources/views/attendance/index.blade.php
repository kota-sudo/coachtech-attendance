@extends('layouts.app')

@php
    use App\Enums\AttendanceStatus;
@endphp

@section('title', '勤怠打刻')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/attendance-register.css') }}">
@endsection

@section('content')
<div class="attendance__content">
    <div class="attendance__status">
        <p class="attendance__status--item">{{ $status->label() }}</p>
    </div>
    <div class="attendance__form">
        <div class="current-date">
            <input class="current-date__item" type="text" value="{{ $todayLabel }}" readonly>
        </div>
        <div class="current-time">
            <input class="current-time__item" type="text" id="current-time" readonly>
        </div>
        <div class="attendance__button">
            @if ($status === AttendanceStatus::OffDuty)
                <form method="POST" action="/attendance/clock-in" style="display:inline;">
                    @csrf
                    <button class="attendance__button--submit--clock-in" type="submit">出勤</button>
                </form>
            @elseif ($status === AttendanceStatus::Working)
                <form method="POST" action="/attendance/clock-out" style="display:inline;">
                    @csrf
                    <button class="attendance__button--submit--clock-out" type="submit">退勤</button>
                </form>
                <form method="POST" action="/attendance/break-in" style="display:inline;">
                    @csrf
                    <button class="attendance__button--submit--break-in" type="submit">休憩入</button>
                </form>
            @elseif ($status === AttendanceStatus::Breaking)
                <form method="POST" action="/attendance/break-out" style="display:inline;">
                    @csrf
                    <button class="attendance__button--submit--break-out" type="submit">休憩戻</button>
                </form>
            @else
                <p class="attendance__message">お疲れ様でした。</p>
            @endif
        </div>
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
    function tick() { el.value = formatter.format(new Date()); }
    tick();
    setInterval(tick, 1000);
})();
</script>
@endsection
