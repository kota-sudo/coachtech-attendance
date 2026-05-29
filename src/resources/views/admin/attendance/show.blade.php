@extends('layouts.app')

@section('title', '勤怠詳細（管理者）')


@section('content')
    <div class="list-card detail-card">
        @if (session('status'))
            <p class="flash-status">{{ session('status') }}</p>
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
                <dt>出勤時間</dt>
                <dd>{{ $attendance->clock_in?->timezone('Asia/Tokyo')->format('H:i') }}</dd>
            </div>
            <div class="detail-row">
                <dt>退勤時間</dt>
                <dd>{{ $attendance->clock_out?->timezone('Asia/Tokyo')->format('H:i') }}</dd>
            </div>
            <div class="detail-row">
                <dt>休憩時間</dt>
                <dd>
                    @forelse ($attendance->breakTimes as $break)
                        <div>{{ $break->break_start?->timezone('Asia/Tokyo')->format('H:i') }} 〜 {{ $break->break_end?->timezone('Asia/Tokyo')->format('H:i') }}</div>
                    @empty
                        <span>—</span>
                    @endforelse
                </dd>
            </div>
            <div class="detail-row">
                <dt>備考</dt>
                <dd>{{ $attendance->note ?: '—' }}</dd>
            </div>
        </dl>

        @if ($hasPending)
            <p class="detail-pending-message">承認待ちのため修正はできません。</p>
        @else
            <form method="POST" action="{{ route('admin.attendance.update', $attendance) }}" class="detail-form">
                @csrf

                <h2 class="detail-form-title">勤怠修正</h2>

                <div class="form-group">
                    <label for="clock_in">出勤時間</label>
                    <input type="time" id="clock_in" name="clock_in" value="{{ $defaultClockIn }}" required>
                    @error('clock_in')<p class="error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label for="clock_out">退勤時間</label>
                    <input type="time" id="clock_out" name="clock_out" value="{{ $defaultClockOut }}" required>
                    @error('clock_out')<p class="error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label>休憩時間</label>
                    @for ($i = 0; $i < $breakRowCount; $i++)
                        @php
                            $break = $attendance->breakTimes[$i] ?? null;
                            $breakStart = old("breaks.{$i}.break_start", $break?->break_start?->timezone('Asia/Tokyo')->format('H:i'));
                            $breakEnd = old("breaks.{$i}.break_end", $break?->break_end?->timezone('Asia/Tokyo')->format('H:i'));
                        @endphp
                        <div class="break-input-row">
                            <input type="time" name="breaks[{{ $i }}][break_start]" value="{{ $breakStart }}">
                            <span>〜</span>
                            <input type="time" name="breaks[{{ $i }}][break_end]" value="{{ $breakEnd }}">
                        </div>
                        @error("breaks.{$i}.break_start")<p class="error">{{ $message }}</p>@enderror
                        @error("breaks.{$i}.break_end")<p class="error">{{ $message }}</p>@enderror
                    @endfor
                </div>

                <div class="form-group">
                    <label for="note">備考</label>
                    <textarea id="note" name="note" rows="4" required>{{ $defaultNote }}</textarea>
                    @error('note')<p class="error">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="btn btn-primary">修正</button>
            </form>
        @endif
    </div>
@endsection
