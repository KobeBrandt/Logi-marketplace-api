<?php

namespace Tests\Feature;

use Tests\TestCase;

class PrivacyTest extends TestCase
{
    private const PAGES = ['/', '/discord-bot', '/discord-bot/terms', '/discord-bot/privacy'];

    public function test_pages_set_no_cookies(): void
    {
        foreach (self::PAGES as $page) {
            $response = $this->get($page)->assertOk();

            $this->assertSame([], $response->headers->getCookies(), "$page sets a cookie.");
        }
    }

    public function test_pages_load_their_fonts_from_this_site(): void
    {
        foreach (self::PAGES as $page) {
            $this->get($page)
                ->assertSee(asset('fonts/outfit.css'))
                ->assertDontSee('fonts.bunny.net');
        }

        $this->assertFileExists(public_path('fonts/outfit.css'));
    }

    public function test_every_page_links_to_the_privacy_policy(): void
    {
        foreach (self::PAGES as $page) {
            $this->get($page)->assertSee(route('discord-bot.privacy'));
        }
    }

    public function test_the_short_privacy_link_redirects_to_the_policy(): void
    {
        $this->get('/privacy')->assertStatus(301)->assertRedirect('/discord-bot/privacy');
    }

    public function test_the_policy_covers_what_gdpr_article_13_asks_for(): void
    {
        $this->get('/discord-bot/privacy')->assertOk()->assertSeeInOrder([
            'Kobe Brandt',
            'contact@logiplugin.com',
            '/privacy',
            '90 days',
            '30 days',
            'Art. 6(1)(b)',
            'Art. 6(1)(f)',
            'OVHcloud',
            'United States',
            'restrict or object',
            'one month',
            'data protection authority',
        ]);
    }
}
