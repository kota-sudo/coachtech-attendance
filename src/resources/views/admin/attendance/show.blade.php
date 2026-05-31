@extends('layouts.admin-app')

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

    <form class="form" method="POST" action="/admin/attendance/{{ $attendance->id }}">
        @csrf

        @if ($hasPending)
            <div class="form__content">
                <div class="form__group">
                    <p class="form__header">名前</p>
                    <div class="form__input-group">
                        <input class="form__input form__input--name readonly" type="text" value="{{ $attendance->user->name }}" readonly>
                    </div>
                </div>
                <div class="form__group">
                    <p class="form__header">日付</p>
                    <div class="form__input-group">
                        <input class="form__input readonly" type="text" value="{{ $year }}" readonly>
                        <input class="form__input readonly" type="text" value="{{ $datePart }}" readonly>
                    </div>
                </div>
                <div class="form__group">
                    <p class="form__header">出勤・退勤</p>
                    <div class="form__input-group">
                        <input class="form__input readonly" type="text" value="{{ $pendingClockIn }}" readonly>
                        <p>〜</p>
                        <input class="form__input readonly" type="text" value="{{ $pendingClockOut }}" readonly>
                    </div>
                </div>
                <div class="form__group form__break-group">
                    <p class="form__header">休憩</p>
                    <div class="form__input-wrapper">
                        @forelse ($pendingBreaks as $break)
                            <div class="form__input-group">
                                <input class="form__input readonly" type="text" value="{{ $break['start'] }}" readonly>
                                <p>〜</p>
                                <input class="form__input readonly" type="text" value="{{ $break['end'] }}" readonly>
                            </div>
                        @empty
                            <div class="form__input-group"><input class="form__input readonly" type="text" value="" readonly></div>
                        @endforelse
                    </div>
                </div>
                <div class="form__group">
                    <p class="form__header">備考</p>
                    <div class="form__input-group">
                        <textarea class="form__textarea readonly" readonly>{{ $pendingNote }}</textarea>
                    </div>
                </div>
            </div>
            <div class="form__button">
                <p class="readonly-message">承認待ちのため修正できません</p>
            </div>
        @else
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
                        <input class="form__input" type="text" value="{{ $year }}" readonly>
                        <input class="form__input" type="text" value="{{ $datePart }}" readonly>
                    </div>
                </div>
                <div class="form__group">
                    <p class="form__header">出勤・退勤</p>
                    <div class="form__input-group">
                        <input class="form__input" type="text" name="clock_in" value="{{ $defaultClockIn }}">
                        <p>〜</p>
                        <input class="form__input" type="text" name="clock_out" value="{{ $defaultClockOut }}">
                    </div>
                </div>
                <div class="error-message">
                    <div></div>
                    <div class="error-message__item">
                        @error('clock_in')<p>{{ $message }}</p>@enderror
                        @error('clock_out')<p>{{ $message }}</p>@enderror
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
                                <input class="form__input" type="text" name="breaks[{{ $i }}][break_start]" value="{{ $breakStart }}">
                                <p>〜</p>
                                <input class="form__input" type="text" name="breaks[{{ $i }}][break_end]" value="{{ $breakEnd }}">
                            </div>
                        @endfor
                    </div>
                </div>
                <div class="error-message">
                    <div></div>
                    <div class="error-message__item">
                        @foreach ($errors->get('breaks.*.break_start') as $messages)
                            @foreach ((array) $messages as $message)<p>{{ $message }}</p>@endforeach
                        @endforeach
                        @foreach ($errors->get('breaks.*.break_end') as $messages)
                            @foreach ((array) $messages as $message)<p>{{ $message }}</p>@endforeach
                        @endforeach
                    </div>
                </div>
                <div class="form__group">
                    <p class="form__header">備考</p>
                    <div class="form__input-group">
                        <textarea class="form__textarea" name="note">{{ $defaultNote }}</textarea>
                    </div>
                </div>
                <div class="error-message">
                    <div></div>
                    <div class="error-message__item">
                        @error('note')<p>{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
            <div class="form__button">
                <button class="form__button--submit" type="submit">修正</button>
            </div>
        @endif
    </form>
</div>
@endsection
