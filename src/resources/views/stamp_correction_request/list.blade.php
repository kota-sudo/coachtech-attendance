@extends('layouts.app')

@php
    use App\Enums\AttendanceCorrectionRequestStatus;
@endphp

@section('title', '申請一覧')

@section('header-actions')
    <a href="{{ route('attendance.list') }}" class="nav-link">勤怠一覧</a>
    <form class="inline" method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="link">ログアウト</button>
    </form>
@endsection

@section('content')
    <div class="list-card">
        <nav class="tab-nav">
            <a href="{{ route('stamp_correction_request.list', ['status' => 'pending']) }}"
               class="tab-link {{ $status === AttendanceCorrectionRequestStatus::Pending ? 'is-active' : '' }}">
                承認待ち
            </a>
            <a href="{{ route('stamp_correction_request.list', ['status' => 'approved']) }}"
               class="tab-link {{ $status === AttendanceCorrectionRequestStatus::Approved ? 'is-active' : '' }}">
                承認済み
            </a>
        </nav>

        <div class="table-wrap">
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th>状態</th>
                        <th>名前</th>
                        <th>対象日時</th>
                        <th>申請理由</th>
                        <th>申請日時</th>
                        <th>詳細</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requests as $correctionRequest)
                        <tr>
                            <td>{{ $correctionRequest->status === AttendanceCorrectionRequestStatus::Pending ? '承認待ち' : '承認済み' }}</td>
                            <td>{{ $correctionRequest->attendance->user->name }}</td>
                            <td>{{ $correctionRequest->attendance->work_date->locale('ja')->isoFormat('YYYY年M月D日(ddd)') }}</td>
                            <td class="cell-note">{{ $correctionRequest->requested_note }}</td>
                            <td>{{ $correctionRequest->created_at->timezone('Asia/Tokyo')->format('Y/m/d H:i') }}</td>
                            <td>
                                <a href="{{ route('attendance.detail', $correctionRequest->attendance_id) }}">詳細</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">申請がありません</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
