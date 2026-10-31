<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') · Logi Marketplace Discord Bot</title>
    <meta name="description" content="@yield('description')">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700" rel="stylesheet">
    <style>
        /* Same colour tokens as the API docs page, so both parts of the site look alike. */
        :root {
            color-scheme: light;
            --bg: #fbfbfb;
            --surface: #ffffff;
            --surface-muted: #f4f4f6;
            --border: rgba(34, 36, 37, 0.1);
            --border-strong: rgba(34, 36, 37, 0.3);
            --text: rgba(34, 36, 37, 0.9);
            --text-muted: rgba(34, 36, 37, 0.6);
            --hover: rgba(0, 0, 0, 0.05);
            --accent: #814efa;
            --accent-hover: #6c38ea;
            --accent-soft: rgba(129, 78, 250, 0.1);
            --card-shadow: 0 1px 2px rgba(0, 0, 0, 0.04), 0 1px 4px rgba(0, 0, 0, 0.04);
            --radius: 10px;
            --topbar-height: 56px;
            --font: "Outfit", "Segoe UI", -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
            --mono: "Cascadia Code", "SF Mono", Menlo, Consolas, monospace;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                color-scheme: dark;
                --bg: #131315;
                --surface: #1c1c1f;
                --surface-muted: #26262a;
                --border: rgba(255, 255, 255, 0.1);
                --border-strong: rgba(255, 255, 255, 0.28);
                --text: rgba(255, 255, 255, 0.92);
                --text-muted: rgba(255, 255, 255, 0.6);
                --hover: rgba(255, 255, 255, 0.06);
                --accent: #9b74ff;
                --accent-hover: #ad8dff;
                --accent-soft: rgba(155, 116, 255, 0.16);
                --card-shadow: none;
            }
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--text-muted);
            font-family: var(--font);
            font-size: 15px;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        a { color: var(--accent); text-decoration: none; }
        a:hover { text-decoration: underline; }

        :focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }

        code { font-family: var(--mono); font-size: 13px; padding: 1px 5px; border-radius: 5px; background: var(--surface-muted); color: var(--text); }

        h1, h2, h3 { margin: 0; color: var(--text); font-weight: 600; line-height: 1.25; letter-spacing: -0.01em; }
        h1 { font-size: 36px; }
        h2 { font-size: 24px; }
        h3 { font-size: 16px; }

        .eyebrow { margin: 0 0 8px; color: var(--accent); font-size: 13px; font-weight: 600; }

        /* ---------- Top bar ---------- */

        .topbar {
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            height: var(--topbar-height);
            padding: 0 24px;
            background: color-mix(in srgb, var(--bg) 85%, transparent);
            border-bottom: 1px solid var(--border);
            backdrop-filter: blur(8px);
        }

        .brand { display: flex; align-items: center; gap: 10px; color: var(--text); font-size: 16px; font-weight: 600; }
        .brand:hover { text-decoration: none; }

        .brand-mark {
            display: grid;
            place-items: center;
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: var(--accent);
            color: #fff;
        }

        .topbar-actions { display: flex; align-items: center; gap: 8px; }

        .topbar-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 999px;
            color: var(--text);
            font-size: 13px;
        }

        .topbar-link:hover { background: var(--hover); text-decoration: none; }
        .topbar-link[aria-current="page"] { background: var(--accent-soft); color: var(--accent); }

        /* ---------- Buttons ---------- */

        .button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border: 1px solid var(--border-strong);
            border-radius: 999px;
            color: var(--text);
            font-weight: 500;
        }

        .button:hover { background: var(--hover); text-decoration: none; }
        .button.primary { border-color: var(--accent); background: var(--accent); color: #fff; }
        .button.primary:hover { border-color: var(--accent-hover); background: var(--accent-hover); }

        /* ---------- Page content ---------- */

        main { max-width: 880px; margin: 0 auto; padding: 56px 24px 72px; }

        .card {
            padding: 24px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: var(--surface);
            box-shadow: var(--card-shadow);
        }

        /* Long-form text such as the terms and the privacy policy. */
        .prose h2 { margin: 40px 0 12px; font-size: 20px; }
        .prose h3 { margin: 24px 0 8px; }
        .prose p, .prose ul { margin: 0 0 12px; }
        .prose li { margin-bottom: 4px; }
        .prose strong { color: var(--text); font-weight: 600; }

        .updated { margin: 8px 0 0; font-size: 13px; }

        /* ---------- Footer ---------- */

        footer {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px 24px;
            padding: 24px 48px 40px;
            border-top: 1px solid var(--border);
            font-size: 13px;
        }

        .footer-links { display: flex; flex-wrap: wrap; align-items: center; gap: 16px; }
        .footer-links a { display: inline-flex; align-items: center; gap: 6px; color: var(--text-muted); }
        .footer-links a:hover { color: var(--text); text-decoration: none; }

        @media (max-width: 700px) {
            .topbar { padding: 0 16px; }
            .topbar-link.optional { display: none; }
            main { padding: 32px 16px 48px; }
            h1 { font-size: 28px; }
            footer { padding: 24px 16px 32px; }
        }

        @stack('styles')
    </style>
</head>
<body>
    <header class="topbar">
        <a class="brand" href="{{ route('discord-bot.home') }}">
            <span class="brand-mark" aria-hidden="true">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 2v6M15 2v6M6 8h12v4a6 6 0 0 1-12 0zM12 18v4"/></svg>
            </span>
            Marketplace Bot
        </a>
        <nav class="topbar-actions" aria-label="Bot pages">
            <a class="topbar-link optional" href="{{ route('discord-bot.terms') }}" @if (request()->routeIs('discord-bot.terms')) aria-current="page" @endif>Terms</a>
            <a class="topbar-link optional" href="{{ route('discord-bot.privacy') }}" @if (request()->routeIs('discord-bot.privacy')) aria-current="page" @endif>Privacy</a>
            <a class="button primary" href="{{ route('discord-bot.invite') }}">Add to Discord</a>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <p style="margin: 0">
            An unofficial community project. Not affiliated with or endorsed by Logitech or Discord.
        </p>
        <nav class="footer-links" aria-label="Site links">
            <a href="{{ route('discord-bot.terms') }}">Terms of Service</a>
            <a href="{{ route('discord-bot.privacy') }}">Privacy Policy</a>
            <a href="{{ url('/') }}">API docs</a>
            <a href="https://kbrandt.dev" target="_blank" rel="noopener">Built by KBrandt.dev</a>
        </nav>
    </footer>
</body>
</html>
