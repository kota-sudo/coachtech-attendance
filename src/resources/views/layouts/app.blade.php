<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', '勤怠管理')</title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 2rem; background: #f5f5f5; }
        .app-header { max-width: 960px; margin: 0 auto 1.5rem; display: flex; justify-content: space-between; align-items: center; gap: 1rem; }
        .app-header h1 { font-size: 1.25rem; margin: 0; }
        .app-header-actions { display: flex; align-items: center; gap: 1rem; }
        .app-main { max-width: 960px; margin: 0 auto; }
        form.inline { display: inline; }
        button.link, a.nav-link { background: none; border: none; color: #2563eb; cursor: pointer; text-decoration: underline; font-size: 1rem; }
        a.nav-link { display: inline-block; }
        .attendance-card, .list-card { background: #fff; border-radius: 8px; padding: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .attendance-date { font-size: 1.125rem; margin: 0 0 .5rem; text-align: center; }
        .attendance-clock { font-size: 2.5rem; font-weight: 700; margin: 0 0 1.5rem; letter-spacing: .05em; text-align: center; }
        .attendance-status { margin-bottom: 2rem; text-align: center; }
        .attendance-status-label { display: block; font-size: .875rem; color: #666; margin-bottom: .25rem; }
        .attendance-status-value { font-size: 1.25rem; font-weight: 700; }
        .attendance-actions { display: flex; flex-direction: column; gap: .75rem; align-items: center; }
        .attendance-actions form { width: 100%; max-width: 280px; }
        .btn { padding: .875rem 1.5rem; border: none; border-radius: 4px; font-size: 1rem; cursor: pointer; text-decoration: none; display: inline-block; text-align: center; }
        .btn-sm { padding: .5rem 1rem; font-size: .875rem; }
        .btn-primary { background: #000; color: #fff; }
        .btn-primary:hover { background: #333; }
        .btn-secondary { background: #fff; color: #000; border: 1px solid #000; }
        .btn-secondary:hover { background: #f5f5f5; }
        .attendance-message { font-size: 1.125rem; margin: 0; text-align: center; }
        .list-nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
        .list-month { font-size: 1.25rem; margin: 0; }
        .table-wrap { overflow-x: auto; }
        .attendance-table { width: 100%; border-collapse: collapse; font-size: .875rem; }
        .attendance-table th, .attendance-table td { border: 1px solid #e5e5e5; padding: .625rem .75rem; text-align: center; }
        .attendance-table th { background: #f9fafb; font-weight: 600; }
        .attendance-table td:empty::before { content: ''; }
        .attendance-table a { color: #2563eb; }
    </style>
</head>
<body>
    <header class="app-header">
        <h1>@yield('title')</h1>
        <div class="app-header-actions">
            @yield('header-actions')
        </div>
    </header>
    <main class="app-main">
        @yield('content')
    </main>
</body>
</html>
