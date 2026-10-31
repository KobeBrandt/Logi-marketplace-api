{{-- Not legal advice: have this reviewed before relying on it. --}}
@extends('discord-bot.layout')

@section('title', 'Terms of Service')
@section('description', 'The terms for using the unofficial Logi Marketplace Discord bot: what it does, acceptable use, and the limits of what it promises.')

@section('content')
    <article class="prose">
        <p class="eyebrow">Discord bot</p>
        <h1>Terms of Service</h1>
        <p class="updated">Last updated 31 October 2026</p>

        <h2>1. Accepting these terms</h2>
        <p>
            These terms cover the Logi Marketplace Discord bot (“the bot”), a free hobby project run by an
            individual developer, <a href="https://kbrandt.dev" target="_blank" rel="noopener">KBrandt</a>.
            By using the bot or adding it to a Discord server, you agree to these terms. If you do not agree,
            please do not use the bot.
        </p>
        <p>
            Because the bot runs on Discord, you must also follow Discord’s
            <a href="https://discord.com/terms" target="_blank" rel="noopener">Terms of Service</a> and
            <a href="https://discord.com/guidelines" target="_blank" rel="noopener">Community Guidelines</a>.
        </p>
        <p>
            The bot is unofficial. It is not affiliated with or endorsed by Logitech or Discord.
        </p>

        <h2>2. What the bot does</h2>
        <p>
            The bot shows public information from the Logi Marketplace, such as plugins, icon packs and device
            profiles, using the logiplugin.com API. It offers these commands:
        </p>
        <ul>
            <li><code>/plugins</code>, <code>/icon-packs</code> and <code>/profiles</code> to look up marketplace items.</li>
            <li><code>/notifications</code> to get direct messages about updates to items you choose to follow.</li>
            <li><code>/channel-updates</code> to let server members with the Manage Server permission set up a channel that receives marketplace updates.</li>
        </ul>

        <h2>3. Acceptable use</h2>
        <p>Please use the bot fairly. You agree not to:</p>
        <ul>
            <li>Abuse, harass or spam others through the bot.</li>
            <li>Spam commands or send requests in a way meant to flood the bot.</li>
            <li>Try to break, overload, reverse engineer or find ways around the limits of the bot or the API behind it.</li>
            <li>Use the bot to break Discord’s rules or any law.</li>
        </ul>

        <h2>4. Server admins</h2>
        <p>
            If you manage a server, you are responsible for how the bot is set up there, including which
            channels receive marketplace updates and which roles, if any, it mentions. Make sure your setup
            suits your community and follows your server’s own rules.
        </p>

        <h2>5. Third-party content and accuracy</h2>
        <p>
            Marketplace content, including names, descriptions, images and downloads, belongs to Logitech and
            the respective creators. The bot only summarises it and links to it.
        </p>
        <p>
            The information the bot shows comes from a third party and may be outdated, incomplete or wrong.
            There is no guarantee that it is accurate. Always check the original marketplace page before
            relying on it, and download items at your own risk.
        </p>

        <h2>6. Availability</h2>
        <p>
            The bot is free and provided “as is” and “as available”, without any warranty. It may change, be
            unavailable, or stop running entirely at any time, with or without notice. Notifications and
            channel posts may arrive late or not at all.
        </p>

        <h2>7. Limitation of liability</h2>
        <p>
            To the extent the law allows, the developer is not liable for any loss or damage that comes from
            using, or not being able to use, the bot, or from relying on the information it shows. This
            includes content created by third parties that the bot links to.
        </p>

        <h2>8. Ending use</h2>
        <p>
            You can stop using the bot at any time by removing it from your server, turning off your
            notifications with <code>/notifications</code>, or simply not using its commands.
        </p>
        <p>
            The developer may block users or servers from using the bot if they abuse it or break these terms.
        </p>

        <h2>9. Privacy</h2>
        <p>
            The <a href="{{ route('discord-bot.privacy') }}">Privacy Policy</a> explains what data the bot
            stores and how it is used.
        </p>

        <h2>10. Changes to these terms</h2>
        <p>
            These terms may be updated from time to time. When they change, the “Last updated” date at the
            top of this page changes too. If you keep using the bot after an update, you accept the new terms.
        </p>

        <h2>11. Contact</h2>
        <p>
            Questions, problems or reports? Email
            <a href="mailto:contact@logiplugin.com">contact@logiplugin.com</a>.
        </p>
    </article>
@endsection
