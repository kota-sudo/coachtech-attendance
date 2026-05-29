@extends('layouts.app')

@section('title', '勤怠一覧')

@section('header-actions')
    <a href="{{ route('attendance') }}" class="nav-link">打刻画面</a>
    <form class="inline" method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="link">ログアウト</button>
    </form>
@endsection

@section('content')
    <div class="list-card">
        <div class="list-nav">
            <a href="{{ route('attendance.list', ['month' => $prevMonth]) }}" class="btn btn-secondary btn-sm">前月</a>
            <h2 class="list-month">{{ $monthLabel }}</h2>
            <a href="{{ route('attendance.list', ['month' => $nextMonth]) }}" class="btn btn-secondary btn-sm">翌月</a>
        </div>

        <div class="table-wrap">
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th>日付</th>
                        <th>出勤</th>
                        <th>退勤</th>
                        <th>休憩</th>
                        <th>合計</th>
                        <th>詳細</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr>
                            <td>{{ $row['date_label'] }}</td>
                            <td>{{ $row['clock_in'] }}</td>
                            <td>{{ $row['clock_out'] }}</td>
                            <td>{{ $row['break_total'] }}</td>
                            <td>{{ $row['work_total'] }}</td>
                            <td>
                                @if ($row['attendance_id'])
                                    <a href="{{ route('attendance.detail', $row['attendance_id']) }}">詳細</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
