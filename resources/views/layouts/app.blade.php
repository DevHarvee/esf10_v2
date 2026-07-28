<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-SF10</title>
    <style>
        :root {
            --ink: #1f2f36;
            --paper: #f4f6fb;
            --panel: #ffffff;
            --line: #c8d0ea;
            --primary: #0d8a3a;
            --secondary: #1f2f86;
            --accent: #d61f26;
            --ok: #0b6d2e;
            --bad: #b3181e;
            --gold: #f3b300;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Trebuchet MS", "Segoe UI", sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 15% 10%, rgba(31,47,134,.14), transparent 35%),
                radial-gradient(circle at 85% 90%, rgba(13,138,58,.12), transparent 35%),
                linear-gradient(130deg, #f7f9ff 0%, #edf2ff 48%, #e8eefb 100%);
            min-height: 100vh;
        }
        .shell { display: grid; grid-template-columns: 260px 1fr; min-height: 100vh; }
        .sidebar {
            padding: 24px 18px;
            background: linear-gradient(180deg, #1f2f86 0%, #273ca7 100%);
            color: #fff;
            border-right: 1px solid rgba(255,255,255,.2);
        }
        .brand {
            display: flex;
            gap: 12px;
            align-items: center;
            margin-bottom: 24px;
        }
        .brand img { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; }
        .brand h1 { font-size: 16px; margin: 0; letter-spacing: .4px; }
        .brand p { margin: 0; font-size: 12px; opacity: .85; }
        .nav { display: grid; gap: 8px; }
        .nav a {
            color: #eaf3f5;
            text-decoration: none;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 14px;
            border: 1px solid transparent;
        }
        .nav a:hover { background: rgba(243,179,0,.2); border-color: rgba(243,179,0,.45); }
        .content { padding: 22px; }
        .card {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 16px;
            box-shadow: 0 8px 28px rgba(0,0,0,.06);
            padding: 18px;
            margin-bottom: 16px;
        }
        .head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }
        .head h2 { margin: 0; font-size: 22px; }
        .muted { color: #50607a; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { border: 1px solid var(--line); padding: 8px; font-size: 13px; text-align: left; }
        th { background: #edf2ff; }
        input, select, textarea, button {
            font: inherit;
            padding: 9px 10px;
            border: 1px solid #b6c2e7;
            border-radius: 8px;
            width: 100%;
            background: #fff;
        }
        button, .btn {
            display: inline-block;
            width: auto;
            background: var(--primary);
            color: #fff;
            text-decoration: none;
            border: 0;
            cursor: pointer;
            padding: 9px 14px;
            border-radius: 8px;
        }
        .btn.alt { background: var(--secondary); }
        .btn.warn { background: var(--accent); }
        .grid { display: grid; gap: 12px; }
        .grid.two { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid.three { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .badge { display: inline-block; padding: 3px 7px; border-radius: 999px; font-size: 12px; }
        .ok { background: #e8f7ee; color: var(--ok); }
        .bad { background: #fde8e8; color: var(--bad); }
        .alert { padding: 10px 12px; border-radius: 9px; margin-bottom: 12px; font-size: 13px; }
        .alert.ok { background: #e8f7ee; color: #1f4f32; border: 1px solid #a7dbc0; }
        .alert.err { background: #fde8e8; color: #7a1e1e; border: 1px solid #f4b8b8; }
        @media (max-width: 980px) {
            .shell { grid-template-columns: 1fr; }
            .sidebar { position: sticky; top: 0; z-index: 10; }
            .grid.two, .grid.three { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="shell">
    <aside class="sidebar">
        <div class="brand">
            <img src="{{ asset('images/logo.jpg') }}" alt="logo">
            <div>
                <h1>E-SF10</h1>
                <p>AENHS Registrar</p>
            </div>
        </div>
        @auth
        <nav class="nav">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            @if(auth()->user()->utype === 'admin')
                <a href="{{ route('enrollment.intake') }}">Add Student Records</a>
                <a href="{{ route('students.index') }}">View Students</a>
                <a href="{{ route('settings.index') }}">Settings</a>
                <a href="{{ route('users.index') }}">User Accounts</a>
            @endif
            @if(auth()->user()->utype === 'teacher')
                <a href="{{ route('grades.index') }}">Student Record</a>
            @endif
            <form method="post" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn warn">Logout</button>
            </form>
        </nav>
        @endauth
    </aside>
    <main class="content">
        @if(session('ok'))
            <div class="alert ok">{{ session('ok') }}</div>
        @endif
        @if(session('error'))
            <div class="alert err">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert err">{{ $errors->first() }}</div>
        @endif
        @yield('content')
    </main>
</div>
</body>
</html>
