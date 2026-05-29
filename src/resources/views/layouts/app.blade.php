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
        .detail-card { padding: 1.5rem 2rem; }
        .detail-display { margin: 0 0 2rem; }
        .detail-row { display: grid; grid-template-columns: 120px 1fr; gap: .5rem 1rem; padding: .75rem 0; border-bottom: 1px solid #e5e5e5; }
        .detail-row dt { font-weight: 600; margin: 0; }
        .detail-row dd { margin: 0; }
        .detail-pending-message { color: #dc2626; font-weight: 600; text-align: center; margin: 1rem 0 0; }
        .detail-form-title { font-size: 1.125rem; margin: 0 0 1rem; }
        .detail-form .form-group { margin-bottom: 1.25rem; }
        .detail-form label { display: block; font-weight: 600; margin-bottom: .375rem; font-size: .875rem; }
        .detail-form input[type=time], .detail-form textarea { width: 100%; max-width: 320px; padding: .5rem; border: 1px solid #ccc; border-radius: 4px; font-size: 1rem; }
        .detail-form textarea { max-width: 100%; }
        .break-input-row { display: flex; align-items: center; gap: .5rem; margin-bottom: .5rem; flex-wrap: wrap; }
        .break-input-row input { width: auto; flex: 0 1 140px; }
        .error { color: #dc2626; font-size: .875rem; margin: .25rem 0 0; }
                .tab-nav { display: flex; gap: 0; margin-bottom: 1.5rem; border-bottom: 1px solid #e5e5e5; }
        .tab-link { padding: .75rem 1.5rem; text-decoration: none; color: #666; border-bottom: 2px solid transparent; margin-bottom: -1px; }
        .tab-link.is-active { color: #000; font-weight: 600; border-bottom-color: #000; }
        .tab-link:hover { color: #000; }
        .cell-note { max-width: 240px; text-align: left; word-break: break-word; }
                .staff-name { font-size: 1rem; margin: 0 0 1rem; font-weight: 600; }
        .list-actions { margin-bottom: 1rem; }
        .flash-status { background: #ecfdf5; color: #047857; padding: .75rem; border-radius: 4px; margin: 0 0 1rem; }
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
