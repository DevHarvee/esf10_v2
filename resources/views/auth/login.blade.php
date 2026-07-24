<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | E-SF10</title>
    <style>
        :root {
            --ink: #1f2f36;
            --paper: #f4f6fb;
            --line: #c8d0ea;
            --primary: #0d8a3a;
            --accent: #1f2f86;
            --deep: #1f2f86;
            --red: #d61f26;
            --gold: #f3b300;
            --warn-bg: #fde8e8;
            --warn-border: #f4b8b8;
            --warn-text: #7a1e1e;
            --brick: #d61f26;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Trebuchet MS", "Segoe UI", sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 10% 10%, rgba(31,47,134,.22), transparent 34%),
                radial-gradient(circle at 88% 88%, rgba(13,138,58,.18), transparent 34%),
                linear-gradient(155deg, #f8faff 0%, #edf2ff 48%, #e8eefb 100%);
            display: grid;
            place-items: center;
            padding: 22px;
        }
        .shell {
            width: min(980px, 100%);
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            border: 1px solid var(--line);
            border-radius: 18px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 14px 45px rgba(15,44,56,.12);
        }
        .hero {
            padding: 28px;
            background:
                linear-gradient(160deg, rgba(31,47,134,.9), rgba(39,60,167,.9)),
                url('{{ asset('images/1.jpg') }}') center/cover no-repeat;
            color: #fff;
            position: relative;
        }
        .hero::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(0deg, rgba(0,0,0,.22), rgba(0,0,0,.05));
        }
        .hero-content {
            position: relative;
            z-index: 1;
            height: 100%;
            display: grid;
            align-content: space-between;
            gap: 22px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .brand img {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,.8);
            object-fit: cover;
        }
        .brand h1 {
            margin: 0;
            font-size: 22px;
            letter-spacing: .4px;
        }
        .brand p {
            margin: 2px 0 0;
            font-size: 13px;
            opacity: .9;
        }
        .hero h2 {
            margin: 0;
            font-size: clamp(25px, 4.4vw, 38px);
            line-height: 1.1;
        }
        .hero small {
            display: block;
            opacity: .9;
            line-height: 1.5;
            max-width: 42ch;
            font-size: 13px;
        }
        .panel {
            padding: 28px;
            background: var(--paper);
            display: grid;
            align-content: center;
        }
        .panel h3 {
            margin: 0;
            color: var(--deep);
            font-size: 30px;
        }
        .panel p {
            margin: 7px 0 18px;
            color: #5f6d74;
            font-size: 14px;
        }
        .field { margin-bottom: 11px; }
        label { display: block; font-size: 12px; margin-bottom: 5px; color: #435961; }
        input {
            width: 100%;
            padding: 11px;
            border: 1px solid #b9ae9f;
            border-radius: 9px;
            background: #fff;
            outline: none;
        }
        input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(31,47,134,.15);
        }
        button {
            width: 100%;
            padding: 12px;
            border: 0;
            border-radius: 10px;
            background: var(--brick);
            color: #fff;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: .2px;
        }
        button:hover { background: #a71b1f; }
        .err {
            background: var(--warn-bg);
            color: var(--warn-text);
            border: 1px solid var(--warn-border);
            padding: 9px;
            border-radius: 8px;
            margin-bottom: 12px;
            font-size: 13px;
        }
        .links {
            margin-top: 12px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
            font-size: 13px;
        }
        .links a {
            color: var(--deep);
            text-decoration: none;
            border-bottom: 1px solid rgba(31,47,134,.35);
        }
        @media (max-width: 900px) {
            .shell { grid-template-columns: 1fr; }
            .hero { min-height: 220px; }
        }
    </style>
</head>
<body>
    <section class="shell">
        <aside class="hero">
            <div class="hero-content">
                <div class="brand">
                    <img src="{{ asset('images/logo.jpg') }}" alt="AENHS Logo">
                    <div>
                        <h1>Aparri East National High School</h1>
                        <p>Electronic SF10 JHS Management System</p>
                    </div>
                </div>

                <div>
                    <h2>Welcome back, Registrar and Teachers.</h2>
                    <small>
                        Continue encoding quarterly grades, consolidating records, and preparing SF10 outputs with your updated workflow.
                    </small>
                </div>
            </div>
        </aside>

        <form class="panel" method="post" action="{{ route('login.store') }}">
            @csrf
            <h3>Sign In</h3>
            <p>Access the school records workspace.</p>

            @if(session('error'))
                <div class="err">{{ session('error') }}</div>
            @endif

            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" placeholder="you@example.com" value="{{ old('email') }}" required>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" placeholder="Enter your password" required>
            </div>

            <button type="submit">Login to Dashboard</button>

            <div class="links">
                <a href="{{ url('/') }}">Back to Landing Page</a>
                <span>School Year Records</span>
            </div>
        </form>
    </section>
</body>
</html>
