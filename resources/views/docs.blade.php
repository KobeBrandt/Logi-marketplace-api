<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Plugin Marketplace API</title>
    <meta name="description" content="An unofficial read-only REST API for browsing plugins, icon packs and profiles from the Logi Marketplace.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700" rel="stylesheet">
    <style>
        :root {
            color-scheme: light;
            --bg: #fbfbfb;
            --surface: #ffffff;
            --surface-muted: #f4f4f6;
            --code-bg: #f8f7fc;
            --code-head: #f1eff8;
            --border: rgba(34, 36, 37, 0.1);
            --border-strong: rgba(34, 36, 37, 0.3);
            --text: rgba(34, 36, 37, 0.9);
            --text-muted: rgba(34, 36, 37, 0.6);
            --hover: rgba(0, 0, 0, 0.05);
            --selected: rgba(0, 0, 0, 0.07);
            --accent: #814efa;
            --accent-hover: #6c38ea;
            --accent-soft: rgba(129, 78, 250, 0.1);
            --get-bg: rgba(22, 163, 74, 0.12);
            --get-text: #15803d;
            --success: #16a34a;
            --warning: #c2410c;
            --danger: #dc2626;
            --json-key: #6b3fd4;
            --json-string: #0f7b4f;
            --json-number: #b45309;
            --json-literal: #be185d;
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
                --code-bg: #17171a;
                --code-head: #202024;
                --border: rgba(255, 255, 255, 0.1);
                --border-strong: rgba(255, 255, 255, 0.28);
                --text: rgba(255, 255, 255, 0.92);
                --text-muted: rgba(255, 255, 255, 0.6);
                --hover: rgba(255, 255, 255, 0.06);
                --selected: rgba(255, 255, 255, 0.09);
                --accent: #9b74ff;
                --accent-hover: #ad8dff;
                --accent-soft: rgba(155, 116, 255, 0.16);
                --get-bg: rgba(74, 222, 128, 0.14);
                --get-text: #4ade80;
                --success: #4ade80;
                --warning: #fbbf24;
                --danger: #f87171;
                --json-key: #b9a1ff;
                --json-string: #86efac;
                --json-number: #fcd34d;
                --json-literal: #f9a8d4;
                --card-shadow: none;
            }
        }

        * { box-sizing: border-box; }

        html { scroll-behavior: smooth; scroll-padding-top: calc(var(--topbar-height) + 24px); }

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

        code, pre { font-family: var(--mono); font-size: 13px; }

        p code, td code, li code {
            padding: 1px 5px;
            border-radius: 5px;
            background: var(--surface-muted);
            color: var(--text);
            font-size: 12.5px;
        }

        :focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }

        h1, h2, h3 { margin: 0; color: var(--text); font-weight: 600; line-height: 1.25; letter-spacing: -0.01em; }
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

        .brand-mark {
            display: grid;
            place-items: center;
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: var(--accent);
            color: #fff;
        }

        .version-pill {
            padding: 1px 8px;
            border: 1px solid var(--border);
            border-radius: 999px;
            color: var(--text-muted);
            font-size: 12px;
            font-weight: 500;
        }

        .topbar-actions { display: flex; align-items: center; gap: 8px; }

        .api-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 4px 12px;
            border: 1px solid var(--border);
            border-radius: 999px;
            font-size: 13px;
        }

        .api-status::before { content: ""; width: 8px; height: 8px; border-radius: 50%; background: var(--border-strong); }
        .api-status.online::before { background: var(--success); box-shadow: 0 0 0 3px var(--get-bg); }
        .api-status.offline::before { background: var(--danger); }

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

        /* ---------- Layout ---------- */

        .layout { display: grid; grid-template-columns: 248px minmax(0, 1fr); }

        .sidebar {
            position: sticky;
            top: var(--topbar-height);
            align-self: start;
            height: calc(100vh - var(--topbar-height));
            overflow-y: auto;
            padding: 28px 16px;
            border-right: 1px solid var(--border);
        }

        .nav-group + .nav-group { margin-top: 24px; }

        .nav-label {
            margin: 0 0 6px;
            padding: 0 10px;
            color: var(--text-muted);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .nav-list { display: flex; flex-direction: column; gap: 1px; margin: 0; padding: 0; list-style: none; }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border-radius: 8px;
            color: var(--text);
            font-size: 14px;
        }

        .nav-item:hover { background: var(--hover); text-decoration: none; }
        .nav-item.active { background: var(--selected); color: var(--accent); font-weight: 500; }
        .nav-item code { font-size: 12.5px; }

        main { width: 100%; max-width: 1180px; padding: 48px 48px 64px; min-width: 0; }

        section { margin-bottom: 72px; }

        .section-lead { margin: 8px 0 24px; max-width: 680px; }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--card-shadow);
            padding: 20px;
        }

        /* ---------- Buttons ---------- */

        .button {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border: 1px solid transparent;
            border-radius: 8px;
            font: inherit;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
        }

        .button:hover { text-decoration: none; }
        .button-primary { background: var(--accent); color: #fff; }
        .button-primary:hover { background: var(--accent-hover); }
        .button-secondary { background: var(--surface); border-color: var(--border); color: var(--text); }
        .button-secondary:hover { background: var(--hover); }

        /* ---------- Hero ---------- */

        .hero h1 { max-width: 720px; font-size: clamp(30px, 4vw, 40px); }
        .hero .section-lead { font-size: 17px; }

        .hero-actions { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 32px; }

        .quickstart {
            display: grid;
            grid-template-columns: minmax(0, 5fr) minmax(0, 7fr);
            gap: 32px;
            align-items: center;
            padding: 28px;
            background: var(--surface-muted);
            border: 0;
            box-shadow: none;
        }

        .quickstart p { margin: 6px 0 16px; }

        .base-url {
            display: flex;
            align-items: center;
            gap: 8px;
            max-width: 100%;
            padding: 4px 4px 4px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: var(--surface);
        }

        .base-url code { flex: 1; overflow: hidden; color: var(--text); text-overflow: ellipsis; white-space: nowrap; }

        /* ---------- Code blocks ---------- */

        .code-block {
            overflow: hidden;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: var(--code-bg);
        }

        .code-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            min-height: 40px;
            padding: 0 6px 0 14px;
            background: var(--code-head);
            border-bottom: 1px solid var(--border);
            color: var(--text);
            font-size: 13px;
            font-weight: 500;
        }

        .code-title { display: flex; align-items: center; gap: 8px; min-width: 0; }
        .code-title code { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

        .copy-button {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            flex-shrink: 0;
            padding: 4px 8px;
            border: 0;
            border-radius: 6px;
            background: transparent;
            color: var(--text-muted);
            font: inherit;
            font-size: 12px;
            cursor: pointer;
        }

        .copy-button:hover { background: var(--hover); color: var(--text); }

        .tabs { display: flex; gap: 2px; margin-left: -8px; }

        .tab {
            padding: 10px 8px;
            border: 0;
            border-bottom: 2px solid transparent;
            background: transparent;
            color: var(--text-muted);
            font: inherit;
            font-size: 13px;
            cursor: pointer;
        }

        .tab:hover { color: var(--text); }
        .tab[aria-selected="true"] { border-bottom-color: var(--accent); color: var(--text); }

        pre {
            margin: 0;
            padding: 16px 18px;
            overflow: auto;
            background: var(--code-bg);
            color: var(--text);
            line-height: 1.65;
            /* Wrap long URLs instead of clipping them at the panel edge. */
            white-space: pre-wrap;
            overflow-wrap: anywhere;
            scrollbar-width: thin;
            scrollbar-color: var(--border) transparent;
        }

        .code-block.scroll pre { max-height: 460px; }

        .code-note {
            overflow: hidden;
            color: var(--text-muted);
            font-weight: 400;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .json-key { color: var(--json-key); }
        .json-string { color: var(--json-string); }
        .json-number { color: var(--json-number); }
        .json-literal { color: var(--json-literal); }

        /* ---------- How it works ---------- */

        .flow { display: grid; grid-template-columns: 1fr auto 1fr auto 1fr; gap: 12px; align-items: stretch; }

        .flow-step { display: flex; flex-direction: column; gap: 6px; }
        .flow-step p { margin: 0; }
        .flow-step.highlight { border-color: var(--accent); box-shadow: 0 0 0 1px var(--accent); }

        .icon-tile {
            display: grid;
            place-items: center;
            width: 36px;
            height: 36px;
            margin-bottom: 6px;
            border-radius: 8px;
            background: var(--accent-soft);
            color: var(--accent);
        }

        .flow-arrow { display: grid; place-items: center; color: var(--text-muted); }

        .features { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 24px; margin-top: 32px; }
        .feature { display: flex; gap: 12px; }
        .feature .icon-tile { flex-shrink: 0; margin: 0; }
        .feature p { margin: 2px 0 0; font-size: 14px; }

        /* ---------- Endpoints ---------- */

        .endpoint {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 40px;
            padding: 40px 0;
            border-top: 1px solid var(--border);
        }

        .resource-head { padding-top: 24px; }
        .resource-head + .endpoint { border-top: 0; padding-top: 16px; }
        .resource-head h3 { margin: 0 0 4px; font-size: 24px; }
        .resource-head p { margin: 0; max-width: 680px; }

        .endpoint-head { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; margin-bottom: 12px; }
        .endpoint-head h3 { width: 100%; margin-bottom: 4px; font-size: 20px; }
        .endpoint-path { color: var(--text); font-size: 14px; word-break: break-all; }
        .endpoint-doc > p { margin: 0 0 24px; }

        .method {
            padding: 1px 7px;
            border-radius: 6px;
            background: var(--get-bg);
            color: var(--get-text);
            font-family: var(--mono);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.03em;
        }

        .endpoint-code {
            position: sticky;
            top: calc(var(--topbar-height) + 24px);
            align-self: start;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .params-title {
            margin: 0;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--border);
            color: var(--text);
            font-size: 14px;
            font-weight: 600;
        }

        .params { margin: 0; padding: 0; list-style: none; }
        .params li { padding: 14px 0; border-bottom: 1px solid var(--border); }
        .params p { margin: 4px 0 0; font-size: 14px; }

        .param-name { display: flex; flex-wrap: wrap; align-items: baseline; gap: 8px; }
        .param-name code { color: var(--text); font-weight: 600; }
        .param-type { color: var(--text-muted); font-family: var(--mono); font-size: 12px; }
        .param-optional { color: var(--warning); font-size: 12px; }

        .status-badge { font-family: var(--mono); font-size: 12px; font-weight: 700; }
        .status-badge.ok { color: var(--success); }
        .status-badge.warn { color: var(--warning); }

        /* ---------- Caching & errors ---------- */

        .two-col { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .two-col h3 { margin-bottom: 8px; }

        .table-wrap { overflow-x: auto; }

        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th, td { padding: 10px 12px; border-bottom: 1px solid var(--border); vertical-align: top; }
        tr:last-child td { border-bottom: 0; }
        td:first-child { padding-left: 0; white-space: nowrap; color: var(--text); }

        .status-code { font-family: var(--mono); font-weight: 700; }
        .status-code.ok { color: var(--success); }
        .status-code.warn { color: var(--warning); }
        .status-code.err { color: var(--danger); }

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

        .footer-links { display: flex; align-items: center; gap: 16px; }
        .footer-links a { display: inline-flex; align-items: center; gap: 6px; color: var(--text-muted); }
        .footer-links a:hover { color: var(--text); text-decoration: none; }

        /* ---------- Smaller screens ---------- */

        @media (max-width: 1200px) {
            .endpoint { grid-template-columns: minmax(0, 1fr); gap: 24px; }
            .endpoint-code { position: static; }
        }

        @media (max-width: 900px) {
            .topbar { padding: 0 16px; }
            .topbar-link span { display: none; }

            .layout { grid-template-columns: minmax(0, 1fr); }
            .sidebar { display: none; }
            main { padding: 32px 16px 48px; }
            section { margin-bottom: 56px; }

            .quickstart { grid-template-columns: minmax(0, 1fr); gap: 20px; padding: 20px; }

            .flow { grid-template-columns: 1fr; }
            .flow-arrow { transform: rotate(90deg); }
            .features { grid-template-columns: minmax(0, 1fr); }

            footer { padding: 24px 16px 32px; }
        }

        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <a class="brand" href="#overview">
            <span class="brand-mark" aria-hidden="true">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 2v6M15 2v6M6 8h12v4a6 6 0 0 1-12 0zM12 18v4"/></svg>
            </span>
            Plugin Marketplace API
            <span class="version-pill">v1</span>
        </a>
        <div class="topbar-actions">
            <span class="api-status" id="api-status" role="status">Checking API…</span>
            <a class="topbar-link" href="https://github.com/KobeBrandt/Logi-marketplace-api" target="_blank" rel="noopener">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-label="GitHub repository"><path d="M12 .5C5.65.5.5 5.65.5 12a11.5 11.5 0 0 0 7.86 10.92c.58.1.79-.25.79-.56v-2c-3.2.7-3.87-1.37-3.87-1.37-.52-1.33-1.28-1.68-1.28-1.68-1.04-.71.08-.7.08-.7 1.15.08 1.76 1.19 1.76 1.19 1.03 1.76 2.69 1.25 3.35.96.1-.75.4-1.25.73-1.54-2.55-.29-5.24-1.28-5.24-5.68 0-1.26.45-2.28 1.19-3.09-.12-.29-.52-1.46.11-3.05 0 0 .97-.31 3.17 1.18a11 11 0 0 1 5.77 0c2.2-1.49 3.17-1.18 3.17-1.18.63 1.59.23 2.76.11 3.05.74.81 1.19 1.83 1.19 3.09 0 4.41-2.69 5.39-5.25 5.67.41.36.78 1.06.78 2.14v3.17c0 .31.21.67.8.56A11.5 11.5 0 0 0 23.5 12C23.5 5.65 18.35.5 12 .5Z"/></svg>
                <span>GitHub</span>
            </a>
            <a class="topbar-link" href="https://marketplace.logi.com" target="_blank" rel="noopener">
                <span>Marketplace</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-label="Open Logi Marketplace"><path d="M7 17 17 7M8 7h9v9"/></svg>
            </a>
        </div>
    </header>

    <div class="layout">
        <aside class="sidebar">
            <nav class="nav-group" aria-label="Guide">
                <p class="nav-label">Guide</p>
                <ul class="nav-list" id="guide-nav">
                    <li><a class="nav-item active" href="#overview">Overview</a></li>
                    <li><a class="nav-item" href="#how-it-works">How it works</a></li>
                    <li><a class="nav-item" href="#endpoints">Endpoints</a></li>
                    <li><a class="nav-item" href="#caching">Caching &amp; errors</a></li>
                </ul>
            </nav>

            <nav class="nav-group" aria-label="Plugin endpoints">
                <p class="nav-label">Plugins</p>
                <ul class="nav-list">
                    <li><a class="nav-item" href="#list-plugins"><span class="method">GET</span><code>/plugins</code></a></li>
                    <li><a class="nav-item" href="#show-plugin"><span class="method">GET</span><code>/plugins/{name}</code></a></li>
                </ul>
            </nav>

            <nav class="nav-group" aria-label="Icon pack endpoints">
                <p class="nav-label">Icon packs</p>
                <ul class="nav-list">
                    <li><a class="nav-item" href="#list-icon-packs"><span class="method">GET</span><code>/icon-packs</code></a></li>
                    <li><a class="nav-item" href="#show-icon-pack"><span class="method">GET</span><code>/icon-packs/{name}</code></a></li>
                </ul>
            </nav>

            <nav class="nav-group" aria-label="Profile endpoints">
                <p class="nav-label">Profiles</p>
                <ul class="nav-list">
                    <li><a class="nav-item" href="#list-profiles"><span class="method">GET</span><code>/profiles</code></a></li>
                    <li><a class="nav-item" href="#show-profile"><span class="method">GET</span><code>/profiles/{name}</code></a></li>
                </ul>
            </nav>
        </aside>

        <main>
            <section id="overview" class="hero">
                <p class="eyebrow">Unofficial · Read-only · No API key</p>
                <h1>Browse the Logi Marketplace with one simple API</h1>
                <p class="section-lead">
                    A read-only REST API for the plugins, icon packs and profiles listed on the Logi Marketplace.
                    Search, filter by platform, category or device, and get full details as clean JSON.
                </p>
                <div class="hero-actions">
                    <a class="button button-primary" href="#endpoints">View endpoints</a>
                    <a class="button button-secondary" href="{{ $apiUrl }}/plugins?per_page=5" target="_blank" rel="noopener">
                        Try it in your browser
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7M8 7h9v9"/></svg>
                    </a>
                </div>

                <div class="card quickstart">
                    <div>
                        <h3>Quickstart</h3>
                        <p>Make your first request in seconds. There's nothing to sign up for. Every endpoint lives under this base URL:</p>
                        <div class="base-url">
                            <code id="base-url">{{ $apiUrl }}</code>
                            <button type="button" class="copy-button" data-copy="#base-url">Copy</button>
                        </div>
                    </div>

                    <div class="code-block" data-tabs>
                        <div class="code-head">
                            <div class="tabs" role="tablist" aria-label="Language">
                                <button type="button" class="tab" role="tab" aria-selected="true" data-tab="curl">cURL</button>
                                <button type="button" class="tab" role="tab" aria-selected="false" data-tab="js">JavaScript</button>
                                <button type="button" class="tab" role="tab" aria-selected="false" data-tab="python">Python</button>
                                <button type="button" class="tab" role="tab" aria-selected="false" data-tab="php">PHP</button>
                            </div>
                            <button type="button" class="copy-button">Copy</button>
                        </div>
                        <pre data-panel="curl"><code>curl "{{ $apiUrl }}/plugins?search=spotify"</code></pre>
                        <pre data-panel="js" hidden><code>const response = await fetch('{{ $apiUrl }}/plugins?search=spotify');
const { data } = await response.json();

console.log(data);</code></pre>
                        <pre data-panel="python" hidden><code>import requests

response = requests.get("{{ $apiUrl }}/plugins", params={"search": "spotify"})
print(response.json()["data"])</code></pre>
                        <pre data-panel="php" hidden><code>$response = file_get_contents('{{ $apiUrl }}/plugins?search=spotify');
$plugins = json_decode($response, true)['data'];

print_r($plugins);</code></pre>
                    </div>
                </div>
            </section>

            <section id="how-it-works">
                <p class="eyebrow">Guide</p>
                <h2>How it works</h2>
                <p class="section-lead">
                    This service sits between your app and the marketplace. It fetches the marketplace data,
                    cleans it up, and serves it through a small, stable API.
                </p>

                <div class="flow">
                    <div class="card flow-step">
                        <div class="icon-tile">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="12" rx="2"/><path d="M8 20h8M12 16v4"/></svg>
                        </div>
                        <h3>Your app</h3>
                        <p>Sends a normal HTTP <code>GET</code> request, with optional filters in the query string.</p>
                    </div>
                    <div class="flow-arrow" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </div>
                    <div class="card flow-step highlight">
                        <div class="icon-tile">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 7v5c0 4.5 3.4 8.4 8 9 4.6-.6 8-4.5 8-9V7z"/><path d="m9 12 2 2 4-4"/></svg>
                        </div>
                        <h3>This API</h3>
                        <p>Checks your input, filters and pages the results, and keeps marketplace data cached for {{ $serverCacheMinutes }} minutes.</p>
                    </div>
                    <div class="flow-arrow" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </div>
                    <div class="card flow-step">
                        <div class="icon-tile">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9h18l-1.5 10.5a2 2 0 0 1-2 1.5h-11a2 2 0 0 1-2-1.5z"/><path d="M8 9V7a4 4 0 0 1 8 0v2"/></svg>
                        </div>
                        <h3>Logi Marketplace</h3>
                        <p>The original source. It's only contacted when the cache is empty or expired.</p>
                    </div>
                </div>

                <div class="features">
                    <div class="feature">
                        <div class="icon-tile">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 3 14h9l-1 8 10-12h-9z"/></svg>
                        </div>
                        <div><h3>Fast</h3><p>Most requests come straight from the cache, without waiting on the marketplace.</p></div>
                    </div>
                    <div class="feature">
                        <div class="icon-tile">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3H6a2 2 0 0 0-2 2v4a2 2 0 0 1-2 2 2 2 0 0 1 2 2v4a2 2 0 0 0 2 2h2M16 3h2a2 2 0 0 1 2 2v4a2 2 0 0 0 2 2 2 2 0 0 0-2 2v4a2 2 0 0 1-2 2h-2"/></svg>
                        </div>
                        <div><h3>Clean data</h3><p>Plugins, icon packs and profiles are flattened into one consistent shape, including platforms and download links.</p></div>
                    </div>
                    <div class="feature">
                        <div class="icon-tile">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.6 13.4 13.4 20.6a2 2 0 0 1-2.8 0L3 13V3h10l7.6 7.6a2 2 0 0 1 0 2.8z"/><circle cx="7.5" cy="7.5" r="1.5"/></svg>
                        </div>
                        <div><h3>Versioned</h3><p>Every URL starts with <code>/v1</code>, so future breaking changes won't affect your integration.</p></div>
                    </div>
                </div>
            </section>

            <section id="endpoints">
                <p class="eyebrow">API reference</p>
                <h2>Endpoints</h2>
                <p class="section-lead">Every endpoint returns JSON. Send <code>Accept: application/json</code> so errors come back as JSON too.</p>

                <div class="resource-head" id="plugins">
                    <h3>Plugins</h3>
                    <p>Plugins add actions for apps like Spotify, OBS or Photoshop to Logitech devices.</p>
                </div>

                <div class="endpoint" id="list-plugins">
                    <div class="endpoint-doc">
                        <div class="endpoint-head">
                            <h3>List plugins</h3>
                            <span class="method">GET</span>
                            <code class="endpoint-path">/api/v1/plugins</code>
                        </div>
                        <p>Lists plugins, one page at a time. All query parameters are optional and can be combined.</p>

                        <p class="params-title">Query parameters</p>
                        <ul class="params">
                            <li>
                                <div class="param-name"><code>search</code><span class="param-type">string</span><span class="param-optional">optional</span></div>
                                <p>Matches the plugin name or display name. Case-insensitive, up to 100 characters.</p>
                            </li>
                            <li>
                                <div class="param-name"><code>platform</code><span class="param-type">string</span><span class="param-optional">optional</span></div>
                                <p>Only plugins for this platform, for example <code>Windows</code> or <code>MacOS</code>.</p>
                            </li>
                            <li>
                                <div class="param-name"><code>category</code><span class="param-type">string</span><span class="param-optional">optional</span></div>
                                <p>Only plugins in this category, for example <code>Gaming</code>.</p>
                            </li>
                            <li>
                                <div class="param-name"><code>page</code><span class="param-type">integer</span><span class="param-optional">optional</span></div>
                                <p>Page number, starting at 1. Default: 1.</p>
                            </li>
                            <li>
                                <div class="param-name"><code>per_page</code><span class="param-type">integer</span><span class="param-optional">optional</span></div>
                                <p>Results per page, from 1 to {{ $maxPerPage }}. Default: {{ $defaultPerPage }}.</p>
                            </li>
                        </ul>
                    </div>

                    <div class="endpoint-code">
                        <div class="code-block">
                            <div class="code-head">
                                <span class="code-title"><span class="method">GET</span><code>/plugins</code></span>
                                <button type="button" class="copy-button">Copy</button>
                            </div>
                            <pre><code>curl "{{ $apiUrl }}/plugins?search=dice&amp;category=Gaming"</code></pre>
                        </div>

                        <div class="code-block scroll">
                            <div class="code-head">
                                <span class="code-title"><span class="status-badge ok">200</span> Response <span class="code-note">· <code>meta.links</code> omitted</span></span>
                                <button type="button" class="copy-button">Copy</button>
                            </div>
                            <pre><code class="json">{
  "data": [
    {
      "id": 4942,
      "name": "Dice",
      "displayName": "Dice",
      "description": "Allows quick virtual dice rolling.",
      "categories": ["Gaming"],
      "icon": "https://marketplace.logi.com/resources/16/Dice_65255eb975.png",
      "versions": ["1.2.1"],
      "platforms": ["MacOS", "Windows"],
      "firstPublicAt": "2025-10-06T17:04:48.177Z",
      "downloads": 8352
    }
  ],
  "links": {
    "first": "{{ $apiUrl }}/plugins?search=dice&category=Gaming&page=1",
    "last": "{{ $apiUrl }}/plugins?search=dice&category=Gaming&page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "{{ $apiUrl }}/plugins",
    "per_page": {{ $defaultPerPage }},
    "to": 1,
    "total": 1
  }
}</code></pre>
                        </div>
                    </div>
                </div>

                <div class="endpoint" id="show-plugin">
                    <div class="endpoint-doc">
                        <div class="endpoint-head">
                            <h3>Get a plugin</h3>
                            <span class="method">GET</span>
                            <code class="endpoint-path">/api/v1/plugins/{name}</code>
                        </div>
                        <p>
                            Returns one plugin, looked up by its <code>name</code> (not the display name).
                            It has all the list fields plus author, links, requirements, and the full version history
                            (here <code>versions</code> is a list of objects instead of version numbers).
                        </p>

                        <p class="params-title">Path parameters</p>
                        <ul class="params">
                            <li>
                                <div class="param-name"><code>name</code><span class="param-type">string</span></div>
                                <p>The plugin's <code>name</code>, as returned by the list endpoint. Case-insensitive.</p>
                            </li>
                        </ul>
                    </div>

                    <div class="endpoint-code">
                        <div class="code-block">
                            <div class="code-head">
                                <span class="code-title"><span class="method">GET</span><code>/plugins/{name}</code></span>
                                <button type="button" class="copy-button">Copy</button>
                            </div>
                            <pre><code>curl "{{ $apiUrl }}/plugins/Dice"</code></pre>
                        </div>

                        <div class="code-block scroll">
                            <div class="code-head">
                                <span class="code-title"><span class="status-badge ok">200</span> Response</span>
                                <button type="button" class="copy-button">Copy</button>
                            </div>
                            <pre><code class="json">{
  "data": {
    "id": 4942,
    "name": "Dice",
    "displayName": "Dice",
    "description": "Allows quick virtual dice rolling.",
    "categories": ["Gaming"],
    "icon": "https://marketplace.logi.com/resources/16/Dice_65255eb975.png",
    "versions": [
      {
        "version": "1.2.1",
        "fileUrl": "https://marketplace.logi.com/resources/16/Dice_1_2_1_33fbfc1963.lplug4",
        "minLPSVersion": "6",
        "platforms": ["MacOS", "Windows"]
      }
    ],
    "platforms": ["MacOS", "Windows"],
    "firstPublicAt": "2025-10-06T17:04:48.177Z",
    "downloads": 8352,
    "author": { "name": "KBrandt.dev", "supportUrl": "" },
    "homeUrl": "",
    "requirements": [],
    "capabilities": [],
    "supportedFeatures": []
  }
}</code></pre>
                        </div>
                    </div>
                </div>

                <div class="resource-head" id="icon-packs">
                    <h3>Icon packs</h3>
                    <p>Icon packs are sets of button icons for apps like Ableton Live or Premiere Pro.</p>
                </div>

                <div class="endpoint" id="list-icon-packs">
                    <div class="endpoint-doc">
                        <div class="endpoint-head">
                            <h3>List icon packs</h3>
                            <span class="method">GET</span>
                            <code class="endpoint-path">/api/v1/icon-packs</code>
                        </div>
                        <p>Lists icon packs, one page at a time. All query parameters are optional and can be combined.</p>

                        <p class="params-title">Query parameters</p>
                        <ul class="params">
                            <li>
                                <div class="param-name"><code>search</code><span class="param-type">string</span><span class="param-optional">optional</span></div>
                                <p>Matches the icon pack name or display name. Case-insensitive, up to 100 characters.</p>
                            </li>
                            <li>
                                <div class="param-name"><code>category</code><span class="param-type">string</span><span class="param-optional">optional</span></div>
                                <p>Only icon packs in this category, for example <code>Animated</code> or <code>Creative</code>.</p>
                            </li>
                            <li>
                                <div class="param-name"><code>page</code><span class="param-type">integer</span><span class="param-optional">optional</span></div>
                                <p>Page number, starting at 1. Default: 1.</p>
                            </li>
                            <li>
                                <div class="param-name"><code>per_page</code><span class="param-type">integer</span><span class="param-optional">optional</span></div>
                                <p>Results per page, from 1 to {{ $maxPerPage }}. Default: {{ $defaultPerPage }}.</p>
                            </li>
                        </ul>
                    </div>

                    <div class="endpoint-code">
                        <div class="code-block">
                            <div class="code-head">
                                <span class="code-title"><span class="method">GET</span><code>/icon-packs</code></span>
                                <button type="button" class="copy-button">Copy</button>
                            </div>
                            <pre><code>curl "{{ $apiUrl }}/icon-packs?search=ableton"</code></pre>
                        </div>

                        <div class="code-block scroll">
                            <div class="code-head">
                                <span class="code-title"><span class="status-badge ok">200</span> Response <span class="code-note">· shortened, <code>links</code> omitted</span></span>
                                <button type="button" class="copy-button">Copy</button>
                            </div>
                            <pre><code class="json">{
  "data": [
    {
      "id": 1587,
      "name": "AbletonLiveIcons",
      "displayName": "Ableton Live by Logi",
      "categories": ["Creative"],
      "icon": "https://marketplace.logi.com/resources/7/Ableton_Live_Icons_48ae8115b9.png",
      "cardIcon": "https://marketplace.logi.com/resources/7/Icon_Pack_Cover_Ableton_c538a0d400.png",
      "firstPublicAt": "2024-11-07T15:19:32.410Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "{{ $apiUrl }}/icon-packs",
    "per_page": {{ $defaultPerPage }},
    "to": 2,
    "total": 2
  }
}</code></pre>
                        </div>
                    </div>
                </div>

                <div class="endpoint" id="show-icon-pack">
                    <div class="endpoint-doc">
                        <div class="endpoint-head">
                            <h3>Get an icon pack</h3>
                            <span class="method">GET</span>
                            <code class="endpoint-path">/api/v1/icon-packs/{name}</code>
                        </div>
                        <p>Returns one icon pack, looked up by its <code>name</code>. It has all the list fields plus the description, author, the <code>.licons</code> download link in <code>package</code>, and a preview of every icon.</p>

                        <p class="params-title">Path parameters</p>
                        <ul class="params">
                            <li>
                                <div class="param-name"><code>name</code><span class="param-type">string</span></div>
                                <p>The icon pack's <code>name</code>, as returned by the list endpoint. Case-insensitive.</p>
                            </li>
                        </ul>
                    </div>

                    <div class="endpoint-code">
                        <div class="code-block">
                            <div class="code-head">
                                <span class="code-title"><span class="method">GET</span><code>/icon-packs/{name}</code></span>
                                <button type="button" class="copy-button">Copy</button>
                            </div>
                            <pre><code>curl "{{ $apiUrl }}/icon-packs/AbletonLiveIcons"</code></pre>
                        </div>

                        <div class="code-block scroll">
                            <div class="code-head">
                                <span class="code-title"><span class="status-badge ok">200</span> Response <span class="code-note">· <code>previewImages</code> shortened</span></span>
                                <button type="button" class="copy-button">Copy</button>
                            </div>
                            <pre><code class="json">{
  "data": {
    "id": 1587,
    "name": "AbletonLiveIcons",
    "displayName": "Ableton Live by Logi",
    "categories": ["Creative"],
    "icon": "https://marketplace.logi.com/resources/7/Ableton_Live_Icons_48ae8115b9.png",
    "cardIcon": "https://marketplace.logi.com/resources/7/Icon_Pack_Cover_Ableton_c538a0d400.png",
    "firstPublicAt": "2024-11-07T15:19:32.410Z",
    "description": "This icon pack for Ableton Live offers a collection of icons that are perfect for customizing your music production flow.",
    "copyright": "Copyright © 2024 Logitech Europe SA. All rights reserved.",
    "author": { "name": "Logitech" },
    "package": "https://marketplace.logi.com/resources/7/Ableton_Live_Icons_421cca8871.licons",
    "previewImages": [
      {
        "url": "https://marketplace.logi.com/resources/12/Zoom_Out_cfa436284b.svg",
        "tooltip": "ZoomOut"
      }
    ]
  }
}</code></pre>
                        </div>
                    </div>
                </div>

                <div class="resource-head" id="profiles">
                    <h3>Profiles</h3>
                    <p>
                        Profiles are ready-made button layouts for a device and app. Profiles inside a profile group
                        (for example all Photoshop profiles) are included, with the group's name in <code>group</code>.
                    </p>
                </div>

                <div class="endpoint" id="list-profiles">
                    <div class="endpoint-doc">
                        <div class="endpoint-head">
                            <h3>List profiles</h3>
                            <span class="method">GET</span>
                            <code class="endpoint-path">/api/v1/profiles</code>
                        </div>
                        <p>Lists profiles, one page at a time. All query parameters are optional and can be combined.</p>

                        <p class="params-title">Query parameters</p>
                        <ul class="params">
                            <li>
                                <div class="param-name"><code>search</code><span class="param-type">string</span><span class="param-optional">optional</span></div>
                                <p>Matches the profile name or display name. Case-insensitive, up to 100 characters.</p>
                            </li>
                            <li>
                                <div class="param-name"><code>platform</code><span class="param-type">string</span><span class="param-optional">optional</span></div>
                                <p>Only profiles for this platform, for example <code>Windows</code> or <code>MacOS</code>.</p>
                            </li>
                            <li>
                                <div class="param-name"><code>category</code><span class="param-type">string</span><span class="param-optional">optional</span></div>
                                <p>Only profiles in this category, for example <code>Audio</code> or <code>Creativity</code>.</p>
                            </li>
                            <li>
                                <div class="param-name"><code>device</code><span class="param-type">string</span><span class="param-optional">optional</span></div>
                                <p>Only profiles for this device, for example <code>MX Creative Keypad</code>.</p>
                            </li>
                            <li>
                                <div class="param-name"><code>page</code><span class="param-type">integer</span><span class="param-optional">optional</span></div>
                                <p>Page number, starting at 1. Default: 1.</p>
                            </li>
                            <li>
                                <div class="param-name"><code>per_page</code><span class="param-type">integer</span><span class="param-optional">optional</span></div>
                                <p>Results per page, from 1 to {{ $maxPerPage }}. Default: {{ $defaultPerPage }}.</p>
                            </li>
                        </ul>
                    </div>

                    <div class="endpoint-code">
                        <div class="code-block">
                            <div class="code-head">
                                <span class="code-title"><span class="method">GET</span><code>/profiles</code></span>
                                <button type="button" class="copy-button">Copy</button>
                            </div>
                            <pre><code>curl "{{ $apiUrl }}/profiles?search=essentials&amp;device=Actions%20Ring"</code></pre>
                        </div>

                        <div class="code-block scroll">
                            <div class="code-head">
                                <span class="code-title"><span class="status-badge ok">200</span> Response <span class="code-note">· shortened, <code>links</code> omitted</span></span>
                                <button type="button" class="copy-button">Copy</button>
                            </div>
                            <pre><code class="json">{
  "data": [
    {
      "id": 297,
      "name": "PhotoshopEssentials",
      "displayName": "Photoshop Essentials",
      "description": "Popular actions for a wide-range of editing activities.",
      "categories": ["Creativity"],
      "icon": "https://marketplace.logi.com/resources/3/S_Wicon_Photoshop_7a10872947_b931dc57b0.png",
      "group": "photoshop",
      "devices": ["MX Creative Keypad", "MX Creative Dial", "Actions Ring"],
      "platforms": ["MacOS", "Windows"],
      "requiredPlugins": ["Photoshop"],
      "firstPublicAt": null
    }
  ],
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "{{ $apiUrl }}/profiles",
    "per_page": {{ $defaultPerPage }},
    "to": 4,
    "total": 4
  }
}</code></pre>
                        </div>
                    </div>
                </div>

                <div class="endpoint" id="show-profile">
                    <div class="endpoint-doc">
                        <div class="endpoint-head">
                            <h3>Get a profile</h3>
                            <span class="method">GET</span>
                            <code class="endpoint-path">/api/v1/profiles/{name}</code>
                        </div>
                        <p>Returns one profile, looked up by its <code>name</code>. It has all the list fields plus the author and one package per device, each with its <code>.lp5</code> download link in <code>fileUrl</code>.</p>

                        <p class="params-title">Path parameters</p>
                        <ul class="params">
                            <li>
                                <div class="param-name"><code>name</code><span class="param-type">string</span></div>
                                <p>The profile's <code>name</code>, as returned by the list endpoint. Case-insensitive.</p>
                            </li>
                        </ul>
                    </div>

                    <div class="endpoint-code">
                        <div class="code-block">
                            <div class="code-head">
                                <span class="code-title"><span class="method">GET</span><code>/profiles/{name}</code></span>
                                <button type="button" class="copy-button">Copy</button>
                            </div>
                            <pre><code>curl "{{ $apiUrl }}/profiles/PhotoshopEssentials"</code></pre>
                        </div>

                        <div class="code-block scroll">
                            <div class="code-head">
                                <span class="code-title"><span class="status-badge ok">200</span> Response <span class="code-note">· <code>packages</code> shortened</span></span>
                                <button type="button" class="copy-button">Copy</button>
                            </div>
                            <pre><code class="json">{
  "data": {
    "id": 297,
    "name": "PhotoshopEssentials",
    "displayName": "Photoshop Essentials",
    "description": "Popular actions for a wide-range of editing activities.",
    "categories": ["Creativity"],
    "icon": "https://marketplace.logi.com/resources/3/S_Wicon_Photoshop_7a10872947_b931dc57b0.png",
    "group": "photoshop",
    "devices": ["MX Creative Keypad", "MX Creative Dial", "Actions Ring"],
    "platforms": ["MacOS", "Windows"],
    "requiredPlugins": ["Photoshop"],
    "firstPublicAt": null,
    "author": { "name": "Logi" },
    "packages": [
      {
        "name": "PhotoshopEssentials-Loupedeck70",
        "version": "1.0.9019.28997",
        "fileUrl": "https://marketplace.logi.com/resources/4/Default_Profile70_f00d76177d.lp5",
        "device": "MX Creative Keypad",
        "platforms": ["MacOS", "Windows"]
      }
    ]
  }
}</code></pre>
                        </div>
                    </div>
                </div>
            </section>

            <section id="caching">
                <p class="eyebrow">Guide</p>
                <h2>Caching &amp; errors</h2>
                <p class="section-lead">What to expect when responses are reused, and how errors look.</p>

                <div class="two-col">
                    <div class="card">
                        <h3>Caching</h3>
                        <div class="table-wrap">
                            <table>
                                <tbody>
                                    <tr><td>Server cache</td><td>Marketplace data is kept for <strong>{{ $serverCacheMinutes }} minutes</strong>, so new plugins can take that long to show up.</td></tr>
                                    <tr><td>Your cache</td><td>Successful responses send <code>Cache-Control: public, max-age=300</code>, so you can reuse them for 5 minutes.</td></tr>
                                    <tr><td>ETag</td><td>Send the <code>ETag</code> back as <code>If-None-Match</code> and you'll get <code>304 Not Modified</code> if nothing changed.</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card">
                        <h3>Status codes</h3>
                        <div class="table-wrap">
                            <table>
                                <tbody>
                                    <tr><td><span class="status-code ok">200</span></td><td>Success.</td></tr>
                                    <tr><td><span class="status-code ok">304</span></td><td>Not modified. Use your cached copy.</td></tr>
                                    <tr><td><span class="status-code warn">404</span></td><td>No plugin, icon pack or profile with that name, or an unknown URL.</td></tr>
                                    <tr><td><span class="status-code warn">405</span></td><td>Wrong method. Only <code>GET</code> and <code>HEAD</code> are allowed.</td></tr>
                                    <tr><td><span class="status-code warn">422</span></td><td>A query parameter is invalid. See <code>errors</code>.</td></tr>
                                    <tr><td><span class="status-code err">502</span></td><td>The marketplace couldn't be reached. Try again later.</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <p>Every error has a <code>message</code>. Invalid input also includes <code>errors</code>.</p>
                <div class="code-block">
                    <div class="code-head">
                        <span class="code-title"><span class="status-badge warn">422</span> Error response</span>
                        <button type="button" class="copy-button">Copy</button>
                    </div>
                    <pre><code class="json">{
  "message": "The per page field must not be greater than {{ $maxPerPage }}.",
  "errors": {
    "per_page": ["The per page field must not be greater than {{ $maxPerPage }}."]
  }
}</code></pre>
                </div>
            </section>
        </main>
    </div>

    <footer>
        <p style="margin: 0">
            An unofficial community project. Not affiliated with or endorsed by Logitech.
            Plugin data comes from the public Logi Marketplace.
        </p>
        <nav class="footer-links" aria-label="Project links">
            <a href="https://kbrandt.dev" target="_blank" rel="noopener">Built by KBrandt.dev</a>
            <a href="https://github.com/KobeBrandt/Logi-marketplace-api" target="_blank" rel="noopener">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 .5C5.65.5.5 5.65.5 12a11.5 11.5 0 0 0 7.86 10.92c.58.1.79-.25.79-.56v-2c-3.2.7-3.87-1.37-3.87-1.37-.52-1.33-1.28-1.68-1.28-1.68-1.04-.71.08-.7.08-.7 1.15.08 1.76 1.19 1.76 1.19 1.03 1.76 2.69 1.25 3.35.96.1-.75.4-1.25.73-1.54-2.55-.29-5.24-1.28-5.24-5.68 0-1.26.45-2.28 1.19-3.09-.12-.29-.52-1.46.11-3.05 0 0 .97-.31 3.17 1.18a11 11 0 0 1 5.77 0c2.2-1.49 3.17-1.18 3.17-1.18.63 1.59.23 2.76.11 3.05.74.81 1.19 1.83 1.19 3.09 0 4.41-2.69 5.39-5.25 5.67.41.36.78 1.06.78 2.14v3.17c0 .31.21.67.8.56A11.5 11.5 0 0 0 23.5 12C23.5 5.65 18.35.5 12 .5Z"/></svg>
                GitHub
            </a>
        </nav>
    </footer>

    <script>
        const API_URL = @json($apiUrl);
    </script>
    @verbatim
    <script>
        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function highlightJson(json) {
            return escapeHtml(json).replace(
                /(&quot;(?:[^&]|&(?!quot;))*&quot;)(\s*:)?|\b(true|false|null)\b|-?\b\d+(?:\.\d+)?\b/g,
                (match, string, colon, literal) => {
                    if (string) {
                        return colon
                            ? `<span class="json-key">${string}</span>${colon}`
                            : `<span class="json-string">${string}</span>`;
                    }

                    return literal
                        ? `<span class="json-literal">${match}</span>`
                        : `<span class="json-number">${match}</span>`;
                },
            );
        }

        async function checkStatus() {
            const status = document.getElementById('api-status');

            try {
                const response = await fetch(`${API_URL}/plugins?per_page=1`, { headers: { Accept: 'application/json' } });
                status.classList.add(response.ok ? 'online' : 'offline');
                // Only a 502 means Logitech's marketplace failed; anything else is our own API.
                if (response.ok) {
                    status.textContent = 'API online';
                } else if (response.status === 502) {
                    status.textContent = 'Marketplace unavailable';
                } else {
                    status.textContent = `API error (${response.status})`;
                }
            } catch (error) {
                status.classList.add('offline');
                status.textContent = 'API offline';
            }
        }

        function highlightNav() {
            const guideLinks = [...document.querySelectorAll('#guide-nav a')];

            const observer = new IntersectionObserver((entries) => {
                for (const entry of entries) {
                    if (!entry.isIntersecting) continue;

                    const id = entry.target.id;
                    guideLinks.forEach((link) => link.classList.toggle('active', link.hash === `#${id}`));
                }
            }, { rootMargin: '-30% 0px -60% 0px' });

            document.querySelectorAll('main section').forEach((section) => observer.observe(section));
        }

        function setUpTabs() {
            document.querySelectorAll('[data-tabs]').forEach((block) => {
                const tabs = [...block.querySelectorAll('[data-tab]')];

                tabs.forEach((tab) => {
                    tab.addEventListener('click', () => {
                        tabs.forEach((other) => other.setAttribute('aria-selected', String(other === tab)));
                        block.querySelectorAll('[data-panel]').forEach((panel) => {
                            panel.hidden = panel.dataset.panel !== tab.dataset.tab;
                        });
                    });
                });
            });
        }

        // A copy button copies the element named in data-copy, or else the visible code in its code block.
        function textToCopy(button) {
            if (button.dataset.copy) {
                return document.querySelector(button.dataset.copy).textContent;
            }

            return button.closest('.code-block').querySelector('pre:not([hidden])').textContent;
        }

        document.querySelectorAll('.copy-button').forEach((button) => {
            button.addEventListener('click', async () => {
                try {
                    await navigator.clipboard.writeText(textToCopy(button));
                    button.textContent = 'Copied';
                } catch (error) {
                    button.textContent = 'Press Ctrl+C';
                }
                setTimeout(() => { button.textContent = 'Copy'; }, 1500);
            });
        });

        document.querySelectorAll('code.json').forEach((block) => {
            block.innerHTML = highlightJson(block.textContent);
        });

        checkStatus();
        highlightNav();
        setUpTabs();
    </script>
    @endverbatim
</body>
</html>
