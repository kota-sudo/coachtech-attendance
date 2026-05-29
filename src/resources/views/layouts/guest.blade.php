<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', '勤怠管理')</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: sans-serif; background: #f5f5f5; margin: 0; padding: 2rem; }
        .container { max-width: 480px; margin: 0 auto; background: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        h1 { font-size: 1.5rem; margin: 0 0 1.5rem; text-align: center; }
        label { display: block; margin-bottom: .25rem; font-weight: 600; font-size: .875rem; }
        input { width: 100%; padding: .625rem; margin-bottom: 1rem; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: .75rem; background: #2563eb; color: #fff; border: none; border-radius: 4px; font-size: 1rem; cursor: pointer; }
        button:hover { background: #1d4ed8; }
        .error { color: #dc2626; font-size: .875rem; margin: -.5rem 0 1rem; }
        .links { margin-top: 1rem; text-align: center; font-size: .875rem; }
        .links a { color: #2563eb; }
    </style>
</head>
<body>
    <div class="container">
        @yield('content')
    </div>
</body>
</html>
