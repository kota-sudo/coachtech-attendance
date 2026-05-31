@extends('layouts.admin-app')

@section('title', '勤怠一覧（管理者）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/admin-attendance-list.css') }}">
@endsection

@section('content')
<div class="attendance-list__content">
    <div class="content__header">
        <h2 class="content__header--item">{{ $dateLabel }}の勤怠</h2>
    </div>
    <div class="content__menu">
        <a class="previous-day" href="/admin/attendance/list?date={{ $prevDate }}">前日</a>
        <p class="current-day">{{ \Carbon\Carbon::parse($date)->format('Y/m/d') }}</p>
        <a class="next-day" href="/admin/attendance/list?date={{ $nextDate }}">翌日</a>
    </div>
    <table class="table">
        <tr class="table__row">
            <th class="table__header"><p class="table__header--item">名前</p></th>
            <th class="table__header"><p class="table__header--item">出勤</p></th>
            <th class="table__header"><p class="table__header--item">退勤</p></th>
            <th class="table__header"><p class="table__header--item">休憩</p></th>
            <th class="table__header"><p class="table__header--item">合計</p></th>
            <th class="table__header"><p class="table__header--item">詳細</p></th>
        </tr>
        @foreach ($rows as $row)
            <tr class="table__row">
                <td class="table__description"><p class="table__description--item">{{ $row['name'] }}</p></td>
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
</div>
@endsection
