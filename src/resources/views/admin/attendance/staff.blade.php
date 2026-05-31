@extends('layouts.admin-app')

@section('title', '月次勤怠')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/staff-attendance-list.css') }}">
@endsection

@section('content')
<div class="attendance-list__content">
    <div class="content__header">
        <h2 class="content__header--item">{{ $user->name }}さんの勤怠</h2>
    </div>
    <div class="content__menu">
        <a class="previous-month" href="/admin/attendance/staff/{{ $user->id }}?month={{ $prevMonth }}">前月</a>
        <p class="current-month">{{ $currentMonthDisplay ?? $monthLabel }}</p>
        @if ($canGoNextMonth ?? false)
            <a class="next-month" href="/admin/attendance/staff/{{ $user->id }}?month={{ $nextMonth }}">翌月</a>
        @else
            <div class="next-month-placeholder"></div>
        @endif
    </div>
    <table class="table">
        <tr class="table__row">
            <th class="table__header"><p class="table__header--item">日付</p></th>
            <th class="table__header"><p class="table__header--item">出勤</p></th>
            <th class="table__header"><p class="table__header--item">退勤</p></th>
            <th class="table__header"><p class="table__header--item">休憩</p></th>
            <th class="table__header"><p class="table__header--item">合計</p></th>
            <th class="table__header"><p class="table__header--item">詳細</p></th>
        </tr>
        @foreach ($rows as $row)
            <tr class="table__row">
                <td class="table__description"><p class="table__description--item">{{ $row['date_label'] }}</p></td>
                <td class="table__description"><p class="table__description--item">{{ $row['clock_in'] }}</p></td>
                <td class="table__description"><p class="table__description--item">{{ $row['clock_out'] }}</p></td>
                <td class="table__description"><p class="table__description--item">{{ $row['break_total'] }}</p></td>
                <td class="table__description"><p class="table__description--item">{{ $row['work_total'] }}</p></td>
                <td class="table__description">
                    @if ($row['attendance_id'])
                        <a class="table__item--detail-link" href="/admin/attendance/{{ $row['attendance_id'] }}">詳細</a>
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
    <div class="csv-button">
        <a class="csv-button__submit" href="/admin/attendance/staff/{{ $user->id }}/csv?month={{ $currentMonth }}">CSV出力</a>
    </div>
</div>
@endsection
