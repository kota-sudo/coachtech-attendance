@php
    $user = auth()->user();
@endphp
@if ($user->isAdmin())
    <form action="/admin/logout" method="post">
        @csrf
        <div class="inner__group">
            <a class="inner__group--item {{ request()->routeIs('admin.attendance.list', 'admin.attendance.show') ? 'is-current' : '' }}"
               href="/admin/attendance/list">勤怠一覧</a>
            <a class="inner__group--item {{ request()->routeIs('admin.staff.list', 'admin.attendance.staff.*') ? 'is-current' : '' }}"
               href="/admin/staff/list">スタッフ一覧</a>
            <a class="inner__group--item {{ request()->routeIs('stamp_correction_request.*') ? 'is-current' : '' }}"
               href="/stamp_correction_request/list?status=pending">申請一覧</a>
            <button class="inner__group--item logout-button" type="submit">ログアウト</button>
        </div>
    </form>
@else
    <form action="/logout" method="post">
        @csrf
        <div class="inner__group">
            <a class="inner__group--item {{ request()->routeIs('attendance') ? 'is-current' : '' }}"
               href="/attendance">勤怠</a>
            <a class="inner__group--item {{ request()->routeIs('attendance.list', 'attendance.detail') ? 'is-current' : '' }}"
               href="/attendance/list">勤怠一覧</a>
            <a class="inner__group--item {{ request()->routeIs('stamp_correction_request.*') ? 'is-current' : '' }}"
               href="/stamp_correction_request/list?status=pending">申請</a>
            <button class="inner__group--item logout-button" type="submit">ログアウト</button>
        </div>
    </form>
@endif
