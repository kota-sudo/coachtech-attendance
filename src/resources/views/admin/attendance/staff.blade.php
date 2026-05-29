@extends('layouts.app')

@section('title', '月次勤怠')


@section('content')
    <div class="list-card">
        <p class="staff-name">{{ $user->name }} の勤怠</p>

        <div class="list-nav">
            <a href="{{ route('admin.attendance.staff.show', ['user' => $user->id, 'month' => $prevMonth]) }}" class="btn btn-secondary btn-sm">前月</a>
            <h2 class="list-month">{{ $monthLabel }}</h2>
            <a href="{{ route('admin.attendance.staff.show', ['user' => $user->id, 'month' => $nextMonth]) }}" class="btn btn-secondary btn-sm">翌月</a>
        </div>

        <div class="list-actions">
            <a href="{{ route('admin.attendance.staff.csv', ['user' => $user->id, 'month' => $currentMonth]) }}" class="btn btn-secondary btn-sm">CSV出力</a>
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
                                    <a href="{{ route('admin.attendance.show', $row['attendance_id']) }}">詳細</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
