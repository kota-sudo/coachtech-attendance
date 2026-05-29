@extends('layouts.app')

@php
    use App\Enums\AttendanceCorrectionRequestStatus;
    $attendance = $correctionRequest->attendance;
@endphp

@section('title', '修正申請承認')


@section('content')
    <div class="list-card detail-card">
        @if (session('status'))
            <p class="flash-status">{{ session('status') }}</p>
        @endif

        @if (! $isPending)
            <p class="detail-pending-message" style="color: #047857;">承認済み</p>
        @endif

        <dl class="detail-display">
            <div class="detail-row">
                <dt>名前</dt>
                <dd>{{ $attendance->user->name }}</dd>
            </div>
            <div class="detail-row">
                <dt>日付</dt>
                <dd>{{ $attendance->work_date->locale('ja')->isoFormat('YYYY年M月D日(ddd)') }}</dd>
            </div>
            <div class="detail-row">
                <dt>申請後の出勤時間</dt>
                <dd>{{ $correctionRequest->requested_clock_in?->timezone('Asia/Tokyo')->format('H:i') }}</dd>
            </div>
            <div class="detail-row">
                <dt>申請後の退勤時間</dt>
                <dd>{{ $correctionRequest->requested_clock_out?->timezone('Asia/Tokyo')->format('H:i') }}</dd>
            </div>
            <div class="detail-row">
                <dt>申請後の休憩時間</dt>
                <dd>
                    @forelse ($correctionRequest->correctionBreaks as $break)
                        <div>{{ $break->break_start?->timezone('Asia/Tokyo')->format('H:i') }} 〜 {{ $break->break_end?->timezone('Asia/Tokyo')->format('H:i') }}</div>
                    @empty
                        <span>—</span>
                    @endforelse
                </dd>
            </div>
            <div class="detail-row">
                <dt>申請理由</dt>
                <dd>{{ $correctionRequest->requested_note }}</dd>
            </div>
        </dl>

        @if ($isPending)
            <form method="POST" action="{{ route('stamp_correction_request.approve.store', $correctionRequest) }}" class="detail-form">
                @csrf
                <button type="submit" class="btn btn-primary">承認</button>
            </form>
        @endif
    </div>
@endsection
