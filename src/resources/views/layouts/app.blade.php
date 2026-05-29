<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', '勤怠管理')</title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 2rem; }
        header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        form.inline { display: inline; }
        button.link { background: none; border: none; color: #2563eb; cursor: pointer; text-decoration: underline; }
    </style>
</head>
<body>
    <header>
        <h1>@yield('title')</h1>
        @yield('header-actions')
    </header>
    @yield('content')
</body>
</html>
