{{-- Not legal advice: have this reviewed before relying on it. --}}
@extends('discord-bot.layout')

@section('title', 'Privacy Policy')
@section('description', 'What the Logi Marketplace Discord bot stores, why it stores it, and how to change or delete it.')

@push('styles')
        .data-table { width: 100%; margin: 0 0 16px; border-collapse: collapse; font-size: 14px; }
        .data-table th, .data-table td { padding: 10px 12px; border-bottom: 1px solid var(--border); text-align: left; vertical-align: top; }
        .data-table th { color: var(--text); font-weight: 600; background: var(--surface-muted); }
        .table-wrap { overflow-x: auto; border: 1px solid var(--border); border-radius: var(--radius); margin: 0 0 16px; }
        .table-wrap .data-table { margin: 0; }
        .table-wrap tr:last-child td { border-bottom: 0; }
@endpush

@section('content')
<article class="prose">
    <p class="eyebrow">Discord bot</p>
    <h1>Privacy Policy</h1>
    <p class="updated">Last updated 31 October 2026</p>

    <h2>Who runs the bot</h2>
    <p>
        The Logi Marketplace Discord bot is run by an individual developer, KBrandt
        (<a href="https://kbrandt.dev" target="_blank" rel="noopener">kbrandt.dev</a>).
        It is an unofficial community project and is not affiliated with or endorsed by Logitech or Discord.
        This page explains what the bot stores, why, and how you can change or delete it.
    </p>

    <h2>The short version</h2>
    <ul>
        <li>The bot stores only what it needs to send you or your server the updates you asked for.</li>
        <li>It does not read or store message content.</li>
        <li>It does not store usernames, avatars, email addresses or IP addresses.</li>
        <li>There are no analytics and no ads, and your data is not sold or shared.</li>
    </ul>

    <h2>What the bot can see</h2>
    <p>
        The bot connects to Discord with the <strong>Guilds</strong> and <strong>Guild Messages</strong> gateway intents.
        It does <strong>not</strong> have the Message Content intent, so it cannot read what people write in channels,
        and it does not store any messages. It only acts on the slash commands and buttons you use with it.
    </p>

    <h2>What the bot stores</h2>
    <p>Data is kept in JSON files on the bot's server. There are three kinds:</p>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th scope="col">What</th>
                    <th scope="col">Contents</th>
                    <th scope="col">When it is deleted</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Your notification settings (<code>/notifications</code>)</td>
                    <td>Your Discord user ID, the names of the plugins you follow, and whether you want a DM for new releases.</td>
                    <td>When you unfollow every plugin and turn new-release DMs off.</td>
                </tr>
                <tr>
                    <td>Channel update settings (<code>/channel-updates</code>, set by members with Manage Server)</td>
                    <td>The channel ID, the server ID, which post types the channel gets (new plugins, plugin updates, new icon packs, new profiles) and, optionally, a role ID to ping.</td>
                    <td>When it is removed in the settings panel. All of a server's entries are deleted automatically when the bot is removed from that server.</td>
                </tr>
                <tr>
                    <td>Marketplace snapshot</td>
                    <td>Public marketplace data (plugin names and versions, icon pack and profile names), used to spot what changed. This is not personal data.</td>
                    <td>Replaced as the marketplace changes.</td>
                </tr>
            </tbody>
        </table>
    </div>
    <p>
        Nothing else is stored. Discord itself processes everything that happens on its platform,
        as described in <a href="https://discord.com/privacy" target="_blank" rel="noopener">Discord's privacy policy</a>.
    </p>

    <h2>Error reports</h2>
    <p>
        When the bot logs an error or warning, the text is sent by DM to the developer and appears in the server logs.
        That text may sometimes include the IDs (user, channel or server) involved in what went wrong.
        It is used only to find and fix bugs.
    </p>

    <h2>Marketplace lookups</h2>
    <p>
        To show plugins, icon packs and profiles, the bot asks the logiplugin.com API, which fetches public data from
        marketplace.logi.com. Commands do not send your identity to that API.
    </p>

    <h2>Why the data is used</h2>
    <p>
        The data is used only to provide the features you or your server admins asked for: sending you DMs about the
        plugins you follow, and posting marketplace updates in the channels that were set up for them.
        It is not used for anything else.
    </p>

    <h2>Your choices and rights</h2>
    <ul>
        <li><strong>See or change your settings</strong> at any time with <code>/notifications</code>.</li>
        <li><strong>Delete your data</strong> by unfollowing every plugin and turning new-release DMs off.</li>
        <li><strong>Server admins</strong> can change or remove channel settings with <code>/channel-updates</code>, or remove the bot from the server to delete all of that server's settings.</li>
        <li><strong>Anything else</strong>, such as a question or a request to access or delete data, can be raised by
            <a href="https://github.com/KobeBrandt/Logi-marketplace-api/issues" target="_blank" rel="noopener">opening an issue on GitHub</a>.</li>
    </ul>
    <p>
        If you are in the EU or UK, the GDPR gives you rights to access, correct and delete your personal data and to
        object to how it is used. Because the bot stores so little, the commands above cover most of these.
        Requests made another way will be handled as soon as reasonably possible.
    </p>

    <h2>Children</h2>
    <p>
        Discord requires users to be at least 13, or the minimum age in their country. The bot does not knowingly
        collect anything beyond what is described on this page.
    </p>

    <h2>Changes to this policy</h2>
    <p>
        If this policy changes, the "Last updated" date at the top will change too. Please also read the
        <a href="{{ route('discord-bot.terms') }}">Terms of Service</a>.
    </p>
</article>
@endsection
