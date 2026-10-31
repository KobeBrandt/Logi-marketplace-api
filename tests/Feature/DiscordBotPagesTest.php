<?php

namespace Tests\Feature;

use Tests\TestCase;

class DiscordBotPagesTest extends TestCase
{
    public function test_the_bot_pages_load(): void
    {
        $this->get('/discord-bot')->assertOk()->assertSee(route('discord-bot.invite'));
        $this->get('/discord-bot/terms')->assertOk()->assertSee('Terms of Service');
        $this->get('/discord-bot/privacy')->assertOk()->assertSee('Privacy Policy');
    }

    public function test_the_invite_link_redirects_to_discord(): void
    {
        config(['services.discord_bot.client_id' => '1234567890']);

        $this->get('/discord-bot/invite')->assertRedirect(
            'https://discord.com/oauth2/authorize?client_id=1234567890&permissions=19456&scope=bot%20applications.commands'
        );
    }

    public function test_the_invite_link_is_unavailable_without_a_client_id(): void
    {
        config(['services.discord_bot.client_id' => null]);

        $this->get('/discord-bot/invite')->assertStatus(503);
    }
}
