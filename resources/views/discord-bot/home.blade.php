@extends('discord-bot.layout')

@section('title', 'Discord Bot')
@section('description', 'An unofficial Discord bot that brings Logi Marketplace plugins, icon packs and device profiles into your server.')

@push('styles')
    /* ---------- Home page ---------- */

    .hero { padding-bottom: 48px; }
    .hero h1 { max-width: 640px; }
    .hero-lead { max-width: 600px; margin: 16px 0 28px; font-size: 17px; }
    .hero-actions { display: flex; flex-wrap: wrap; gap: 12px; }

    .home-section { padding-top: 48px; border-top: 1px solid var(--border); margin-top: 8px; }
    .section-lead { max-width: 600px; margin: 8px 0 24px; }

    /* Two columns on wide screens, one on phones (see the media query below). */
    .command-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .command-card h3 { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
    .command-card h3 code { padding: 2px 8px; font-size: 14px; }
    .command-card p { margin: 0; }
    .command-card .note { margin-top: 10px; font-size: 13px; }

    /* Small rounded square that holds a command's icon. */
    .icon-tile {
        display: grid;
        place-items: center;
        flex-shrink: 0;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: var(--accent-soft);
        color: var(--accent);
    }

    /* The card for the server settings command spans the full row. */
    .command-card.wide { grid-column: 1 / -1; }

    .permission-list { display: flex; flex-wrap: wrap; gap: 8px; margin: 0 0 16px; padding: 0; list-style: none; }

    .permission-list li {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border: 1px solid var(--border);
        border-radius: 999px;
        background: var(--surface-muted);
        color: var(--text);
        font-size: 13px;
    }

    .permission-list svg { color: var(--accent); }

    .permissions-card p { margin: 0; }
    .permissions-card p + p { margin-top: 8px; }

    .closing { margin: 48px 0 0; font-size: 14px; text-align: center; }

    @media (max-width: 700px) {
        .command-grid { grid-template-columns: 1fr; }
        .hero { padding-bottom: 32px; }
        .home-section { padding-top: 32px; }
    }
@endpush

@section('content')
    {{-- Hero: what the bot is, with the two main actions. --}}
    <section class="hero">
        <p class="eyebrow">Unofficial · Free · For Discord servers</p>
        <h1>The Logi Marketplace, right inside Discord</h1>
        <p class="hero-lead">
            Browse plugins, icon packs and device profiles for your Logitech gear, and get told when something new comes out.
        </p>
        <div class="hero-actions">
            <a class="button primary" href="{{ route('discord-bot.invite') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                Add to Discord
            </a>
            <a class="button" href="{{ url('/') }}">
                API docs
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
        </div>
    </section>

    {{-- Commands: one card per slash command. --}}
    <section class="home-section" id="commands">
        <p class="eyebrow">Commands</p>
        <h2>What you can do</h2>
        <p class="section-lead">
            Everything works through slash commands. Type <code>/</code> in any channel the bot can see to get started.
        </p>

        <div class="command-grid">
            <article class="card command-card">
                <h3>
                    <span class="icon-tile" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 2v6M15 2v6M6 8h12v4a6 6 0 0 1-12 0zM12 18v4"/></svg>
                    </span>
                    <code>/plugins</code>
                </h3>
                <p>Browse, search or look up a plugin. Filter by platform and category, and change the sort order. Newest plugins come first by default.</p>
            </article>

            <article class="card command-card">
                <h3>
                    <span class="icon-tile" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                    </span>
                    <code>/icon-packs</code>
                </h3>
                <p>Browse, search or look up an icon pack. Filter by category and pick how the results are sorted.</p>
            </article>

            <article class="card command-card">
                <h3>
                    <span class="icon-tile" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h10M18 6h2M4 12h4M12 12h8M4 18h12"/><circle cx="16" cy="6" r="2"/><circle cx="10" cy="12" r="2"/><circle cx="18" cy="18" r="2"/></svg>
                    </span>
                    <code>/profiles</code>
                </h3>
                <p>Browse, search or look up a device profile. Filter by device, platform and category, and pick how the results are sorted.</p>
            </article>

            <article class="card command-card">
                <h3>
                    <span class="icon-tile" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                    </span>
                    <code>/notifications</code>
                </h3>
                <p>Get a DM when a plugin you follow gets an update, or when new plugins, icon packs or profiles are released.</p>
                <p class="note">Each person picks what they follow. Nothing is turned on until you choose it.</p>
            </article>

            <article class="card command-card wide">
                <h3>
                    <span class="icon-tile" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 9h16M4 15h16M10 3 8 21M16 3l-2 18"/></svg>
                    </span>
                    <code>/channel-updates</code>
                </h3>
                <p>
                    Opens a settings panel for your server. Pick which channels get posts about new plugins, plugin updates,
                    new icon packs and new profiles. You can also pick a role to ping with each post.
                </p>
                <p class="note">Only members with the Manage Server permission can use this command.</p>
            </article>
        </div>
    </section>

    {{-- Permissions: what the bot asks for when you add it, and why. --}}
    <section class="home-section" id="permissions">
        <p class="eyebrow">Permissions</p>
        <h2>Only what it needs</h2>
        <p class="section-lead">When you add the bot, it asks for three permissions.</p>

        <div class="card permissions-card">
            <ul class="permission-list">
                <li>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 5 5L20 7"/></svg>
                    View Channel
                </li>
                <li>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 5 5L20 7"/></svg>
                    Send Messages
                </li>
                <li>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 5 5L20 7"/></svg>
                    Embed Links
                </li>
            </ul>
            <p>These are needed to post channel updates in the channels you pick.</p>
            <p>The bot does not read the content of messages in your server.</p>
        </div>
    </section>

    {{-- Closing line with the legal pages. --}}
    <p class="closing">
        By adding the bot you agree to the <a href="{{ route('discord-bot.terms') }}">Terms of Service</a>.
        See the <a href="{{ route('discord-bot.privacy') }}">Privacy Policy</a> for what data is stored.
    </p>
@endsection
