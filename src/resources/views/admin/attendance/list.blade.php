@extends('layouts.app')

@section('title', '勤怠一覧（管理者）')


@section('content')
    <div class="list-card">
        <div class="list-nav">
            <a href="{{ route('admin.attendance.list', ['date' => $prevDate]) }}" class="btn btn-secondary btn-sm">前日</a>
            <h2 class="list-month">{{ $dateLabel }}</h2>
            <a href="{{ route('admin.attendance.list', ['date' => $nextDate]) }}" class="btn btn-secondary btn-sm">翌日</a>
        </div>

        <div class="table-wrap">
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th>名前</th>
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
                            <td>{{ $row['name'] }}</td>
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
