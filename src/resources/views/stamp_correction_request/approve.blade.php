@extends('layouts.admin-app')

@php
    use App\Enums\AttendanceCorrectionRequestStatus;
    $attendance = $correctionRequest->attendance;
@endphp

@section('title', '修正申請承認')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/admin-application-detail.css') }}">
@endsection

@section('content')
<div class="detail__content">
    <div class="detail__header">
        <h2 class="content__header--item">勤怠詳細</h2>
    </div>
    @if (session('status'))
        <p class="flash-status">{{ session('status') }}</p>
    @endif
    <form class="applied-form" method="POST" action="/stamp_correction_request/approve/{{ $correctionRequest->id }}">
        @csrf
        <div class="applied-form__content">
            <div class="applied-form__group">
                <p class="applied-form__header">名前</p>
                <div class="applied-form__input-group">
                    <input class="applied-form__input" type="text" value="{{ $attendance->user->name }}" readonly>
                </div>
            </div>
            <div class="applied-form__group">
                <p class="applied-form__header">日付</p>
                <div class="applied-form__input-group">
                    <input class="applied-form__input" type="text" value="{{ $attendance->work_date->format('Y年') }}" readonly>
                    <input class="applied-form__input" type="text" value="{{ $attendance->work_date->format('m月d日') }}" readonly>
                </div>
            </div>
            <div class="applied-form__group">
                <p class="applied-form__header">出勤・退勤</p>
                <div class="applied-form__input-group">
                    <input class="applied-form__input" type="text" value="{{ $correctionRequest->requested_clock_in?->timezone('Asia/Tokyo')->format('H:i') }}" readonly>
                    <p class="wavy-line">〜</p>
                    <input class="applied-form__input" type="text" value="{{ $correctionRequest->requested_clock_out?->timezone('Asia/Tokyo')->format('H:i') }}" readonly>
                </div>
            </div>
            <div class="applied-form__group form__break-group">
                <p class="applied-form__header">休憩</p>
                <div class="applied-form__input-wrapper">
                    @forelse ($correctionRequest->correctionBreaks as $break)
                        <div class="applied-form__input-group">
                            <input class="applied-form__input readonly" type="text" value="{{ $break->break_start?->timezone('Asia/Tokyo')->format('H:i') }}" readonly>
                            <p>〜</p>
                            <input class="applied-form__input readonly" type="text" value="{{ $break->break_end?->timezone('Asia/Tokyo')->format('H:i') }}" readonly>
                        </div>
                    @empty
                        <span>—</span>
                    @endforelse
                </div>
            </div>
            <div class="applied-form__group">
                <p class="applied-form__header">備考</p>
                <div class="applied-form__input-group">
                    <textarea class="applied-form__textarea" readonly>{{ $correctionRequest->requested_note }}</textarea>
                </div>
            </div>
        </div>
        <div class="applied-form__button">
            @if ($isPending)
                <button class="applied-form__button--submit" type="submit">承認</button>
            @else
                <p class="applied-form__status">承認済み</p>
            @endif
        </div>
    </form>
</div>
@endsection
