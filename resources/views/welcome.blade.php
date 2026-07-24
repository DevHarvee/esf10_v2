<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-SF10 | Landing</title>
    <style>
        :root {
            --navy: #1f2f86;
            --brick: #d61f26;
            --paper: #f4f6fb;
            --panel: #ffffff;
            --line: #c8d0ea;
            --text: #1f2f36;
            --muted: #4e5d73;
            --cta: #0d8a3a;
            --cta-hover: #086d2d;
            --gold: #f3b300;
            --mint: #eaf7ee;
            --sky: #e8efff;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Franklin Gothic Book", "Gill Sans", "Segoe UI", sans-serif;
            color: var(--text);
            background: var(--paper);
        }
        .site-header {
            background: linear-gradient(90deg, var(--navy), #2a46bc);
            color: #fff;
            border-bottom: 4px solid var(--gold);
        }
        .header-wrap {
            max-width: 1100px;
            margin: 0 auto;
            padding: 14px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .brand img {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,.75);
            object-fit: cover;
        }
        .brand h1 {
            margin: 0;
            font-family: Cambria, Georgia, serif;
            font-size: 24px;
            letter-spacing: .2px;
        }
        .brand p { margin: 3px 0 0; font-size: 12px; opacity: .9; }
        .cta {
            color: #fff;
            text-decoration: none;
            background: var(--brick);
            border: 1px solid rgba(255,255,255,.24);
            border-radius: 8px;
            padding: 9px 14px;
            font-weight: 700;
        }
        .cta:hover { background: #b3181e; }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 18px 20px 30px;
        }
        .notice {
            border: 1px solid #c6d5ff;
            background: var(--sky);
            color: #1f3b8f;
            border-radius: 8px;
            padding: 10px 12px;
            margin-bottom: 16px;
            font-size: 13px;
        }
        .main {
            display: grid;
            grid-template-columns: 1.2fr .8fr;
            gap: 16px;
        }
        .card {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 16px;
        }
        .hero-image {
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #aebce9;
            margin-bottom: 12px;
        }
        .hero-image img {
            width: 100%;
            height: 280px;
            object-fit: cover;
            display: block;
        }
        .headline {
            margin: 0 0 8px;
            font-family: Cambria, Georgia, serif;
            font-size: clamp(24px, 4.2vw, 38px);
            line-height: 1.15;
            color: var(--navy);
        }
        .lede {
            margin: 0 0 14px;
            color: var(--muted);
            line-height: 1.6;
            font-size: 15px;
        }
        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-block;
            text-decoration: none;
            border-radius: 8px;
            padding: 10px 13px;
            font-weight: 700;
            border: 1px solid #a8bec7;
        }
        .btn.primary {
            background: var(--cta);
            color: #fff;
            border-color: var(--cta);
        }
        .btn.primary:hover { background: var(--cta-hover); }
        .btn.alt { background: #fff; color: var(--navy); }

        .section-title {
            margin: 0 0 10px;
            font-size: 18px;
            color: var(--navy);
            border-left: 4px solid var(--gold);
            padding-left: 8px;
        }
        .feature-list {
            margin: 0;
            padding-left: 18px;
            line-height: 1.65;
            color: #24433a;
            font-size: 14px;
        }
        .meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .meta-item {
            border: 1px solid #c7d0ed;
            border-radius: 8px;
            padding: 10px;
            background: var(--mint);
            font-size: 13px;
        }
        .meta-item b {
            display: block;
            color: var(--navy);
            margin-bottom: 2px;
        }
        .right-card h3 {
            margin: 0 0 8px;
            font-family: Cambria, Georgia, serif;
            color: var(--navy);
        }
        .right-card p {
            margin: 0 0 10px;
            font-size: 14px;
            line-height: 1.6;
            color: #41565f;
        }
        .quick {
            margin: 0;
            padding: 0;
            list-style: none;
            border: 1px solid #c1cdee;
            border-radius: 8px;
            overflow: hidden;
        }
        .quick li {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            padding: 9px 10px;
            font-size: 13px;
            background: #fff;
            border-bottom: 1px solid #e0e7fb;
        }
        .quick li:last-child { border-bottom: 0; }
        .footer-note {
            margin-top: 14px;
            color: #5b6a70;
            font-size: 12px;
        }
        @media (max-width: 960px) {
            .main { grid-template-columns: 1fr; }
            .header-wrap { flex-direction: column; align-items: flex-start; }
            .hero-image img { height: 220px; }
            .meta { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="header-wrap">
            <div class="brand">
                <img src="{{ asset('images/logo.jpg') }}" alt="AENHS Logo">
                <div>
                    <h1>Aparri East National High School</h1>
                    <p>Electronic SF10 JHS Management System</p>
                </div>
            </div>
            <a class="cta" href="{{ route('login') }}">Staff Login</a>
        </div>
    </header>

    <main class="container">
        <div class="notice">
            This portal is for authorized school personnel only. Please use your assigned account to continue.
        </div>

        <div class="main">
            <article class="card">
                <div class="hero-image">
                    <img src="{{ asset('images/school.jpg') }}" alt="AENHS">
                </div>

                <h2 class="headline">Aparri East National High School Electronic SF10</h2>
                <p class="lede">
                    A working platform for registrar and teacher tasks: enrollment intake, quarterly grade recording,
                    consolidation, and SF10 reporting.
                </p>

                {{-- <div class="actions">
                    <a class="btn primary" href="{{ route('login') }}">Open Portal</a>
                    <a class="btn alt" href="{{ route('login') }}">Registrar and Teacher Access</a>
                </div> --}}

                <h3 class="section-title" style="margin-top:16px;">Core Workflows</h3>
                <ul class="feature-list">
                    <li>New and re-entry student enrollment for current school year</li>
                    <li>Quarter-based grade encoding with MAPEH auto-computation</li>
                    <li>Consolidation from working grades to final records</li>
                    <li>Permanent record viewing, printing, and export</li>
                </ul>
            </article>

            <aside class="card right-card">
                <h3>System Snapshot</h3>
                <p>
                    Designed around the existing E-SF10 process so staff can migrate with minimal retraining.
                </p>

                <div class="meta">
                    <div class="meta-item"><b>Coverage</b>Grades 7 to 10</div>
                    <div class="meta-item"><b>Records</b>SF10 Permanent Files</div>
                    <div class="meta-item"><b>User Roles</b>Registrar and Teacher</div>
                    <div class="meta-item"><b>Output</b>Print and Excel Export</div>
                </div>

                <h3 style="margin-top:14px;">Quick Information</h3>
                <ul class="quick">
                    <li><span>School ID</span><strong>300471</strong></li>
                    <li><span>District</span><strong>FIRST</strong></li>
                    <li><span>Division</span><strong>CAGAYAN</strong></li>
                    <li><span>Region</span><strong>02</strong></li>
                </ul>

                <p class="footer-note">Need access? Contact the registrar to request account credentials.</p>
            </aside>
        </div>
    </main>
</body>
</html>
