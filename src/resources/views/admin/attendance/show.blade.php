@extends('layouts.admin-app')

@section('title', '勤怠詳細（管理者）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/admin-detail.css') }}">
@endsection

@section('content')
<div class="detail__content">
    <div class="detail__header">
        <h2 class="content__header--item">勤怠詳細</h2>
    </div>
    @if (session('status'))
        <p class="flash-status">{{ session('status') }}</p>
    @endif
    @if ($hasPending)
        <div class="form">
            <div class="form__content">
                <div class="form__group">
                    <p class="form__header">名前</p>
                    <div class="form__input-group">
                        <input class="form__input form__input--name" type="text" value="{{ $attendance->user->name }}" readonly>
                    </div>
                </div>
                <div class="form__group">
                    <p class="form__header">日付</p>
                    <div class="form__input-group">
                        <input class="form__input" type="text" value="{{ $attendance->work_date->locale('ja')->isoFormat('YYYY年M月D日(ddd)') }}" readonly>
                    </div>
                </div>
                <div class="form__group">
                    <p class="form__header">出勤・退勤</p>
                    <div class="form__input-group">
                        <input class="form__input" type="text" value="{{ $attendance->clock_in?->timezone('Asia/Tokyo')->format('H:i') }}" readonly>
                        <p>〜</p>
                        <input class="form__input" type="text" value="{{ $attendance->clock_out?->timezone('Asia/Tokyo')->format('H:i') }}" readonly>
                    </div>
                </div>
                <div class="form__group form__break-group">
                    <p class="form__header">休憩</p>
                    <div class="form__input-wrapper">
                        @forelse ($attendance->breakTimes as $break)
                            <div class="form__input-group">
                                <input class="form__input" type="text" value="{{ $break->break_start?->timezone('Asia/Tokyo')->format('H:i') }}" readonly>
                                <p>〜</p>
                                <input class="form__input" type="text" value="{{ $break->break_end?->timezone('Asia/Tokyo')->format('H:i') }}" readonly>
                            </div>
                        @empty
                            <span>—</span>
                        @endforelse
                    </div>
                </div>
                <div class="form__group">
                    <p class="form__header">備考</p>
                    <div class="form__input-group">
                        <textarea class="form__textarea" readonly>{{ $attendance->note ?: '—' }}</textarea>
                    </div>
                </div>
            </div>
            <p class="detail-pending-message">承認待ちのため修正はできません。</p>
        </div>
    @else
        <form class="form" method="POST" action="/admin/attendance/{{ $attendance->id }}">
            @csrf
            <div class="form__content">
                <div class="form__group">
                    <p class="form__header">名前</p>
                    <div class="form__input-group">
                        <input class="form__input form__input--name" type="text" value="{{ $attendance->user->name }}" readonly>
                    </div>
                </div>
                <div class="form__group">
                    <p class="form__header">日付</p>
                    <div class="form__input-group">
                        <input class="form__input" type="text" value="{{ $attendance->work_date->locale('ja')->isoFormat('YYYY年M月D日(ddd)') }}" readonly>
                    </div>
                </div>
                <div class="form__group">
                    <p class="form__header">出勤・退勤</p>
                    <div class="form__input-group">
                        <input class="form__input" type="time" name="clock_in" value="{{ $defaultClockIn }}">
                        <p>〜</p>
                        <input class="form__input" type="time" name="clock_out" value="{{ $defaultClockOut }}">
                    </div>
                </div>
                <div class="form__group form__break-group">
                    <p class="form__header">休憩</p>
                    <div class="form__input-wrapper">
                        @for ($i = 0; $i < $breakRowCount; $i++)
                            @php
                                $break = $attendance->breakTimes[$i] ?? null;
                                $breakStart = old("breaks.{$i}.break_start", $break?->break_start?->timezone('Asia/Tokyo')->format('H:i'));
                                $breakEnd = old("breaks.{$i}.break_end", $break?->break_end?->timezone('Asia/Tokyo')->format('H:i'));
                            @endphp
                            <div class="form__input-group">
                                <input class="form__input" type="time" name="breaks[{{ $i }}][break_start]" value="{{ $breakStart }}">
                                <p>〜</p>
                                <input class="form__input" type="time" name="breaks[{{ $i }}][break_end]" value="{{ $breakEnd }}">
                            </div>
                        @endfor
                    </div>
                </div>
                <div class="form__group">
                    <p class="form__header">備考</p>
                    <div class="form__input-group">
                        <textarea class="form__textarea" name="note" rows="4">{{ $defaultNote }}</textarea>
                    </div>
                </div>
            </div>
            <div class="form__button">
                <button class="form__button--submit" type="submit">修正</button>
            </div>
        </form>
    @endif
</div>
@endsection
