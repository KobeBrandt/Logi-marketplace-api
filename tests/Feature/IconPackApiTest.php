<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IconPackApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    public function test_icon_packs_are_listed_with_category_names(): void
    {
        $this->fakeMarketplace([
            ['id' => 1, 'name' => 'NeonIcons', 'displayName' => 'Neon Icons', 'categoryIds' => [0, 38]],
        ]);

        $this->getJson('/api/v1/icon-packs')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'NeonIcons')
            ->assertJsonPath('data.0.displayName', 'Neon Icons')
            ->assertJsonPath('data.0.categories', ['Creative']);
    }

    public function test_icon_packs_can_be_filtered_by_category(): void
    {
        $this->fakeMarketplace([
            ['id' => 1, 'name' => 'NeonIcons', 'categoryIds' => [38]],
            ['id' => 2, 'name' => 'MovingIcons', 'categoryIds' => [37]],
        ]);

        $this->getJson('/api/v1/icon-packs?category=Animated')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'MovingIcons');
    }

    public function test_icon_packs_can_be_searched(): void
    {
        $this->fakeMarketplace([
            ['id' => 1, 'name' => 'NeonIcons', 'displayName' => 'Neon Icons', 'categoryIds' => []],
            ['id' => 2, 'name' => 'MovingIcons', 'displayName' => 'Moving Icons', 'categoryIds' => []],
        ]);

        $this->getJson('/api/v1/icon-packs?search=neon')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'NeonIcons');
    }

    public function test_icon_packs_are_paginated(): void
    {
        $this->fakeMarketplace(array_map(fn (int $i): array => [
            'name' => "IconPack{$i}",
            'categoryIds' => [],
        ], range(1, 5)));

        $this->getJson('/api/v1/icon-packs?per_page=2&page=2')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.name', 'IconPack3')
            ->assertJsonPath('meta.total', 5)
            ->assertJsonPath('meta.last_page', 3)
            ->assertJsonPath('meta.per_page', 2);
    }

    public function test_icon_pack_details_are_shown(): void
    {
        $this->fakeMarketplace([['id' => 1, 'name' => 'NeonIcons', 'categoryIds' => [38]]]);
        $this->fakeIconPackDetails('NeonIcons');

        $this->getJson('/api/v1/icon-packs/NeonIcons')
            ->assertOk()
            ->assertJsonPath('data.name', 'NeonIcons')
            ->assertJsonPath('data.categories', ['Creative'])
            ->assertJsonPath('data.description', 'Glowing icons.')
            ->assertJsonPath('data.copyright', 'Neon Studio')
            ->assertJsonPath('data.author.name', 'Neon Studio')
            ->assertJsonPath('data.package', 'https://example.com/neon.licons')
            ->assertJsonPath('data.previewImages.0.url', 'https://example.com/preview.png')
            ->assertJsonPath('data.previewImages.0.tooltip', 'Preview');
    }

    public function test_icon_pack_name_lookup_is_case_insensitive(): void
    {
        $this->fakeMarketplace([['id' => 1, 'name' => 'NeonIcons', 'categoryIds' => []]]);
        $this->fakeIconPackDetails('NeonIcons');

        $this->getJson('/api/v1/icon-packs/neonicons')
            ->assertOk()
            ->assertJsonPath('data.name', 'NeonIcons');
    }

    public function test_unknown_icon_pack_returns_not_found(): void
    {
        $this->fakeMarketplace([['id' => 1, 'name' => 'NeonIcons', 'categoryIds' => []]]);

        $response = $this->getJson('/api/v1/icon-packs/UnknownIcons')
            ->assertNotFound()
            ->assertJsonPath('message', 'Icon pack not found.');

        $this->assertStringNotContainsString('public', $response->headers->get('Cache-Control'));
    }

    public function test_per_page_is_limited(): void
    {
        $this->getJson('/api/v1/icon-packs?per_page=101')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('per_page');
    }

    private function fakeMarketplace(array $iconPacks): void
    {
        Http::fake([
            'marketplace.logi.com/plugins/en/4' => Http::response(
                '<script id="__NEXT_DATA__" type="application/json">{"buildId":"currentBuild"}</script>',
            ),
            'marketplace.logi.com/_next/data/currentBuild/icons/en/4.json' => Http::response([
                'pageProps' => [
                    'initialState' => [
                        'iconList' => [
                            'icons' => $iconPacks,
                        ],
                        'iconCategoryList' => [
                            'iconCategories' => [
                                ['id' => 0, 'name' => 'All'],
                                ['id' => 37, 'name' => 'Animated'],
                                ['id' => 38, 'name' => 'Creative'],
                            ],
                        ],
                    ],
                ],
            ]),
        ]);
    }

    private function fakeIconPackDetails(string $name): void
    {
        Http::fake([
            "marketplace.logi.com/_next/data/currentBuild/icon/{$name}/en.json*" => Http::response([
                'pageProps' => [
                    'initialState' => [
                        'iconDetails' => [
                            'icon' => [
                                'name' => $name,
                                'description' => 'Glowing icons.',
                                'copyright' => 'Neon Studio',
                                'package' => 'https://example.com/neon.licons',
                                'previewImages' => [
                                    ['url' => 'https://example.com/preview.png', 'tooltip' => 'Preview'],
                                ],
                                'author' => ['name' => 'Neon Studio'],
                            ],
                        ],
                    ],
                ],
            ]),
        ]);
    }
}
