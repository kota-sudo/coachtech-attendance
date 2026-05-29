@extends('layouts.app')

@section('title', 'スタッフ一覧')


@section('content')
    <div class="list-card">
        <h2 class="list-month" style="margin-bottom: 1.5rem;">スタッフ一覧</h2>

        <div class="table-wrap">
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th>名前</th>
                        <th>メールアドレス</th>
                        <th>月次勤怠</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($staff as $member)
                        <tr>
                            <td>{{ $member->name }}</td>
                            <td>{{ $member->email }}</td>
                            <td>
                                <a href="{{ route('admin.attendance.staff.show', $member) }}">詳細</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
