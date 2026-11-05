{{-- Not legal advice: have this reviewed before relying on it. --}}
{{-- Keep in line with the short notice in the bot's /privacy command, and with what the bot and this site really store and log. --}}
@extends('discord-bot.layout')

@section('title', 'Privacy Policy')
@section('description', 'What the Logi Marketplace Discord bot and logiplugin.com store, why, for how long, and how to see, change or delete it.')

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
    <p class="eyebrow">Discord bot and website</p>
    <h1>Privacy Policy</h1>
    <p class="updated">Last updated 3 November 2026</p>

    <h2>Who is responsible</h2>
    <p>
        The Logi Marketplace Discord bot and the logiplugin.com website and API are run by Kobe Brandt, an individual
        developer (<a href="https://kbrandt.dev" target="_blank" rel="noopener">kbrandt.dev</a>), who is the data controller.
        For anything about your data, email <a href="mailto:contact@logiplugin.com">contact@logiplugin.com</a>.
        Both are unofficial community projects, not affiliated with or endorsed by Logitech or Discord.
    </p>

    <h2>The short version</h2>
    <ul>
        <li>The bot stores only what it needs to send you or your server the updates you asked for.</li>
        <li>It does not see what people write in channels.</li>
        <li>It does not store usernames, avatars, email addresses or IP addresses.</li>
        <li>The website sets no cookies and loads nothing from other companies.</li>
        <li>There are no analytics and no ads, and your data is never sold.</li>
    </ul>

    <h2>What the bot can see</h2>
    <p>
        The bot connects to Discord with only the <strong>Guilds</strong> gateway intent, which tells it about servers,
        channels and roles. It does <strong>not</strong> ask for message events or the Message Content intent, so it does
        not see what people write in channels.
    </p>
    <p>
        When you use one of its slash commands or buttons, Discord sends the bot your user ID, your username, the server
        and channel you used it in, and what you typed. The bot uses this to answer you. It keeps none of it unless you
        turn on notifications, as described below.
    </p>

    <h2>What the bot stores</h2>
    <p>Data is kept in files on the bot's server. There are four kinds:</p>
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
                    <td>Your notification settings (<code>/notifications</code> and Follow buttons)</td>
                    <td>Your Discord user ID, the names of the plugins you follow, whether you want a DM for new releases, and the date DMs to you started failing, if they do. Stored encrypted.</td>
                    <td>
                        When you press <strong>Delete my data</strong> in <code>/privacy</code> or stop all notifications.
                        Also when your Discord account no longer exists (checked daily), and when a DM fails after DMs to you have been failing for 90 days.
                    </td>
                </tr>
                <tr>
                    <td>Channel update settings (<code>/channel-updates</code>, set by members with Manage Server)</td>
                    <td>The channel ID, the server ID, which post types the channel gets (new plugins, plugin updates, new icon packs, new profiles) and, optionally, a role ID to ping.</td>
                    <td>When it is removed in the settings panel, when the bot is removed from that server, or when the bot finds the channel is gone or hidden from it.</td>
                </tr>
                <tr>
                    <td>Marketplace snapshot</td>
                    <td>Public marketplace data (plugin names and versions, icon pack and profile names), used to spot what changed. This is not personal data.</td>
                    <td>Replaced as the marketplace changes.</td>
                </tr>
                <tr>
                    <td>Error logs and reports</td>
                    <td>The command or button involved, and the error. They hold no user IDs, server IDs or usernames. They can hold a server name, a channel ID or the name of a plugin. Reports are sent to the developer as a Discord DM.</td>
                    <td>Docker deletes the oldest log file once the logs reach 30 MB. Reports stay in the developer's DMs until they are removed.</td>
                </tr>
            </tbody>
        </table>
    </div>
    <p>
        Copies of these files in server backups expire within 30 days. Nothing else is stored. Discord itself processes
        everything that happens on its platform, as described in
        <a href="https://discord.com/privacy" target="_blank" rel="noopener">Discord's privacy policy</a>.
    </p>

    <h2>The website and API</h2>
    <p>logiplugin.com sets no cookies and has no analytics. Its fonts and other files all come from its own server.</p>
    <p>
        To answer a request, the server has to receive your IP address, the address you asked for and what your browser or
        app sends along, such as its user agent. The web server logs the time, the address asked for (including any search
        terms in it), the referring page and the user agent. These logs do not hold your IP address. Docker deletes the
        oldest log file once the logs reach 30 MB.
    </p>

    <h2>Marketplace lookups</h2>
    <p>
        To show plugins, icon packs and profiles, the bot asks the logiplugin.com API, which fetches public data from
        marketplace.logi.com. Commands do not send your identity to that API, so text you type in a search reaches its logs
        without anything linking it to you. Only this server contacts marketplace.logi.com, so your IP address is not sent there.
    </p>

    <h2>Marketplace creators</h2>
    <p>
        The bot and the API show public data from the Logi Marketplace, including the author name, support link and copyright
        text that creators published there. If you are a creator, this can be personal data about you that did not come from
        you directly. It is used to show people who made an item and where to get support. The API keeps its copy for at most
        30 minutes, and posts the bot already made stay in their channels until the server removes them.
    </p>
    <p>
        To have your name or link left out of the API and the bot, or to object to this use, email
        <a href="mailto:contact@logiplugin.com">contact@logiplugin.com</a>. Your listing on the Logi Marketplace itself is
        run by Logitech, so changes there are up to you and them.
    </p>

    <h2>Why the data is used, and the legal basis</h2>
    <ul>
        <li><strong>Notification and channel settings:</strong> to provide the features you or your server admins asked for. The legal basis is providing the service you requested (GDPR Art. 6(1)(b)).</li>
        <li><strong>Error logs and web server logs:</strong> to keep the bot and the site working and secure. The legal basis is legitimate interest (Art. 6(1)(f)).</li>
        <li><strong>Creator names and links:</strong> to show who made an item. The legal basis is legitimate interest (Art. 6(1)(f)).</li>
        <li><strong>Email to the contact address:</strong> to answer you. The legal basis is legitimate interest (Art. 6(1)(f)), or the legal duty to handle privacy requests (Art. 6(1)(c)). Your email is deleted once it is no longer needed for that.</li>
    </ul>
    <p>
        Notifications are optional: you can look things up without them. Nothing here involves profiling or decisions
        made automatically about you, beyond the deletion rules above.
    </p>

    <h2>Who receives data</h2>
    <ul>
        <li><strong>OVHcloud</strong> hosts the bot and the website on a server in Germany, and handles email for contact@logiplugin.com.</li>
        <li><strong>Discord</strong> delivers DMs, channel posts and error reports. Discord processes data in the United States under the EU-U.S. Data Privacy Framework.</li>
    </ul>
    <p>Nobody else receives it, and it is never sold.</p>

    <h2>Your choices and rights</h2>
    <ul>
        <li><strong>See or download your data</strong> with <code>/privacy</code>.</li>
        <li><strong>Change your settings</strong> at any time with <code>/notifications</code>.</li>
        <li><strong>Delete your data</strong> with <strong>Delete my data</strong> in <code>/privacy</code>.</li>
        <li><strong>Server admins</strong> can change or remove channel settings with <code>/channel-updates</code>, or remove the bot from the server to delete all of that server's settings.</li>
        <li><strong>Anything else</strong>, such as restricting or objecting to how your data is used, can be sent by email to
            <a href="mailto:contact@logiplugin.com">contact@logiplugin.com</a>.</li>
    </ul>
    <p>
        The GDPR gives you rights to access, correct, delete and take your personal data with you, and to restrict or object
        to how it is used. These apply to everyone who uses the bot or the website, wherever they live. Requests sent by email
        get an answer within one month. You can also complain to the data protection authority in your country.
    </p>

    <h2>Children</h2>
    <p>
        Discord requires users to be at least 13, or the minimum age in their country. The bot does not ask for anyone's age.
        If you think a child's data should be removed, email <a href="mailto:contact@logiplugin.com">contact@logiplugin.com</a>.
    </p>

    <h2>Changes to this policy</h2>
    <p>
        If this policy changes, the "Last updated" date at the top will change too. Please also read the
        <a href="{{ route('discord-bot.terms') }}">Terms of Service</a>.
    </p>
</article>
@endsection
