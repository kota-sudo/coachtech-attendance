<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', '勤怠管理')</title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 2rem; background: #f5f5f5; }
        .app-header { max-width: 640px; margin: 0 auto 1.5rem; display: flex; justify-content: space-between; align-items: center; }
        .app-header h1 { font-size: 1.25rem; margin: 0; }
        .app-main { max-width: 640px; margin: 0 auto; }
        form.inline { display: inline; }
        button.link { background: none; border: none; color: #2563eb; cursor: pointer; text-decoration: underline; font-size: 1rem; }
        .attendance-card { background: #fff; border-radius: 8px; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,.08); text-align: center; }
        .attendance-date { font-size: 1.125rem; margin: 0 0 .5rem; }
        .attendance-clock { font-size: 2.5rem; font-weight: 700; margin: 0 0 1.5rem; letter-spacing: .05em; }
        .attendance-status { margin-bottom: 2rem; }
        .attendance-status-label { display: block; font-size: .875rem; color: #666; margin-bottom: .25rem; }
        .attendance-status-value { font-size: 1.25rem; font-weight: 700; }
        .attendance-actions { display: flex; flex-direction: column; gap: .75rem; align-items: center; }
        .attendance-actions form { width: 100%; max-width: 280px; }
        .btn { width: 100%; padding: .875rem; border: none; border-radius: 4px; font-size: 1rem; cursor: pointer; }
        .btn-primary { background: #000; color: #fff; }
        .btn-primary:hover { background: #333; }
        .btn-secondary { background: #fff; color: #000; border: 1px solid #000; }
        .btn-secondary:hover { background: #f5f5f5; }
        .attendance-message { font-size: 1.125rem; margin: 0; }
    </style>
</head>
<body>
    <header class="app-header">
        <h1>@yield('title')</h1>
        @yield('header-actions')
    </header>
    <main class="app-main">
        @yield('content')
    </main>
</body>
</html>
