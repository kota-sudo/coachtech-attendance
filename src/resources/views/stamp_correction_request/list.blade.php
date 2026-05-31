@extends('layouts.app')

@php
    use App\Enums\AttendanceCorrectionRequestStatus;
@endphp

@section('title', '申請一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/user-application-list.css') }}">
@endsection

@section('content')
<div class="application-list__content">
    <div class="content__header">
        <h2 class="content__header--item">申請一覧</h2>
    </div>

    @if (session('status'))
        <p class="flash-status">{{ session('status') }}</p>
    @endif

    <div class="application__tab">
        <input class="application__tab--input" id="tab1" type="radio" name="tab_item" @checked($status === AttendanceCorrectionRequestStatus::Pending) onchange="window.location.href='/stamp_correction_request/list?status=pending'">
        <label class="application__tab--label" for="tab1">承認待ち</label>
        <input class="application__tab--input" id="tab2" type="radio" name="tab_item" @checked($status === AttendanceCorrectionRequestStatus::Approved) onchange="window.location.href='/stamp_correction_request/list?status=approved'">
        <label class="application__tab--label" for="tab2">承認済み</label>
        <div class="tab__content tab__content--active">
            <table class="table">
                <tr class="table__row">
                    <th class="table__header"><p class="table__header--item">状態</p></th>
                    <th class="table__header"><p class="table__header--item">名前</p></th>
                    <th class="table__header"><p class="table__header--item">対象日時</p></th>
                    <th class="table__header"><p class="table__header--item">申請理由</p></th>
                    <th class="table__header"><p class="table__header--item">申請日時</p></th>
                    <th class="table__header"><p class="table__header--item">詳細</p></th>
                </tr>
                @forelse ($requests as $correctionRequest)
                    <tr class="table__row">
                        <td class="table__description"><p class="table__description--item">{{ $correctionRequest->status === AttendanceCorrectionRequestStatus::Pending ? '承認待ち' : '承認済み' }}</p></td>
                        <td class="table__description"><p class="table__description--item">{{ $correctionRequest->attendance->user->name }}</p></td>
                        <td class="table__description"><p class="table__description--item">{{ $correctionRequest->attendance->work_date->locale('ja')->isoFormat('YYYY年M月D日(ddd)') }}</p></td>
                        <td class="table__description"><p class="table__description--item">{{ $correctionRequest->requested_note ?: 'なし' }}</p></td>
                        <td class="table__description"><p class="table__description--item">{{ $correctionRequest->created_at->timezone('Asia/Tokyo')->format('Y/m/d H:i') }}</p></td>
                        <td class="table__description"><a class="table__item--detail-link" href="/attendance/detail/{{ $correctionRequest->attendance_id }}">詳細</a></td>
                    </tr>
                @empty
                    <tr class="table__row"><td class="table__description" colspan="6"><p class="table__description--item">申請がありません</p></td></tr>
                @endforelse
            </table>
        </div>
    </div>
</div>
@endsection
