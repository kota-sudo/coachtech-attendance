@php
    $user = auth()->user();
@endphp
<nav class="main-nav" aria-label="メインメニュー">
    @if ($user->isAdmin())
        <a href="{{ route('admin.attendance.list') }}"
           class="nav-link {{ request()->routeIs('admin.attendance.list', 'admin.attendance.show') ? 'is-current' : '' }}">日次勤怠一覧</a>
        <a href="{{ route('admin.staff.list') }}"
           class="nav-link {{ request()->routeIs('admin.staff.list', 'admin.attendance.staff.*') ? 'is-current' : '' }}">スタッフ一覧</a>
        <a href="{{ route('stamp_correction_request.list') }}"
           class="nav-link {{ request()->routeIs('stamp_correction_request.*') ? 'is-current' : '' }}">申請一覧</a>
        <form class="inline" method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="link">ログアウト</button>
        </form>
    @else
        <a href="{{ route('attendance') }}"
           class="nav-link {{ request()->routeIs('attendance') ? 'is-current' : '' }}">勤怠打刻</a>
        <a href="{{ route('attendance.list') }}"
           class="nav-link {{ request()->routeIs('attendance.list', 'attendance.detail') ? 'is-current' : '' }}">勤怠一覧</a>
        <a href="{{ route('stamp_correction_request.list') }}"
           class="nav-link {{ request()->routeIs('stamp_correction_request.*') ? 'is-current' : '' }}">申請一覧</a>
        <form class="inline" method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="link">ログアウト</button>
        </form>
    @endif
</nav>
