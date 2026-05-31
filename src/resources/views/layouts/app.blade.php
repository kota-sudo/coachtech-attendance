<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', '勤怠管理')</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    @yield('css')
</head>
<body>
    <header class="header">
        <div class="header__inner">
            <a class="header__logo" href="{{ auth()->user()?->isAdmin() ? '/admin/attendance/list' : '/attendance' }}">
                <img class="header__logo--img" src="{{ asset('images/logo.svg') }}" alt="CoachTech">
            </a>
            @auth
                @include('layouts.partials.main-nav')
            @endauth
        </div>
    </header>
    <main>
        @yield('content')
    </main>
</body>
</html>
