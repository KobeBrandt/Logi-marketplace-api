<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProfileApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    public function test_profiles_include_standalone_and_group_profiles(): void
    {
        $this->fakeMarketplace(
            standalone: [$this->profile('StandaloneProfile', categoryIds: [41])],
            groups: ['photoshop' => [
                $this->profile('PhotoshopProfile', categoryIds: [43]),
                $this->profile('StandaloneProfile'),
            ]],
        );

        $this->getJson('/api/v1/profiles')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.name', 'StandaloneProfile')
            ->assertJsonPath('data.0.group', null)
            ->assertJsonPath('data.0.categories', ['Audio'])
            ->assertJsonPath('data.1.name', 'PhotoshopProfile')
            ->assertJsonPath('data.1.group', 'photoshop')
            ->assertJsonPath('data.1.categories', ['Creativity'])
            ->assertJsonPath('data.1.devices', ['MX Creative Console'])
            ->assertJsonPath('data.1.platforms', ['Windows'])
            ->assertJsonPath('data.1.requiredPlugins', ['AdobePhotoshop']);
    }

    public function test_profiles_can_be_filtered(): void
    {
        $this->fakeMarketplace(
            standalone: [
                $this->profile('AudioProfile', categoryIds: [41], device: 'MX Keys', platform: 'macOS'),
                $this->profile('VideoProfile', categoryIds: [768]),
            ],
        );

        $this->getJson('/api/v1/profiles?category=Audio')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'AudioProfile');

        $this->getJson('/api/v1/profiles?device=MX%20Keys')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'AudioProfile');

        $this->getJson('/api/v1/profiles?platform=Windows')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'VideoProfile');

        $this->getJson('/api/v1/profiles?search=video')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'VideoProfile');
    }

    public function test_profiles_are_paginated(): void
    {
        $this->fakeMarketplace(
            standalone: array_map(fn (int $i): array => $this->profile("Profile{$i}"), range(1, 5)),
        );

        $this->getJson('/api/v1/profiles?per_page=2&page=2')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.name', 'Profile3')
            ->assertJsonPath('meta.total', 5)
            ->assertJsonPath('meta.last_page', 3)
            ->assertJsonPath('meta.per_page', 2);
    }

    public function test_profile_details_leave_out_previews_and_manifests(): void
    {
        $this->fakeMarketplace(groups: ['photoshop' => [$this->profile('PhotoshopProfile')]]);
        $this->fakeProfileDetails('PhotoshopProfile');

        $response = $this->getJson('/api/v1/profiles/PhotoshopProfile')
            ->assertOk()
            ->assertJsonPath('data.name', 'PhotoshopProfile')
            ->assertJsonPath('data.group', 'photoshop')
            ->assertJsonPath('data.author.name', 'Logitech')
            ->assertJsonPath('data.packages.0.version', '1.2.0')
            ->assertJsonPath('data.packages.0.fileUrl', 'https://example.com/PhotoshopProfile.lp5')
            ->assertJsonPath('data.packages.0.device', 'MX Creative Console')
            ->assertJsonPath('data.packages.0.platforms', ['Windows']);

        $this->assertStringNotContainsString('preview', $response->getContent());
        $this->assertStringNotContainsString('manifest', $response->getContent());
    }

    public function test_profile_name_lookup_is_case_insensitive(): void
    {
        $this->fakeMarketplace(standalone: [$this->profile('KnownProfile')]);
        $this->fakeProfileDetails('KnownProfile');

        $this->getJson('/api/v1/profiles/knownprofile')
            ->assertOk()
            ->assertJsonPath('data.name', 'KnownProfile');
    }

    public function test_unknown_profile_returns_not_found(): void
    {
        $this->fakeMarketplace(standalone: [$this->profile('KnownProfile')]);

        $response = $this->getJson('/api/v1/profiles/UnknownProfile')
            ->assertNotFound()
            ->assertJsonPath('message', 'Profile not found.');

        $this->assertStringNotContainsString('public', $response->headers->get('Cache-Control'));
    }

    public function test_empty_profile_list_returns_bad_gateway(): void
    {
        $this->fakeMarketplace();

        $this->getJson('/api/v1/profiles')
            ->assertStatus(502);
    }

    private function profile(
        string $name,
        array $categoryIds = [],
        string $device = 'MX Creative Console',
        string $platform = 'Windows',
    ): array {
        return [
            'id' => crc32($name),
            'name' => $name,
            'displayName' => $name,
            'type' => 'Profile5',
            'categoryIds' => $categoryIds,
            'packages' => [[
                'name' => $name,
                'supportedDevice' => ['id' => 1, 'displayName' => $device],
                'supportedPlatforms' => [['key' => 'win', 'displayName' => $platform]],
            ]],
            'requiredPlugins' => [['id' => 7, 'name' => 'AdobePhotoshop']],
        ];
    }

    private function fakeMarketplace(array $standalone = [], array $groups = []): void
    {
        $fakes = [
            'marketplace.logi.com/plugins/en/4' => Http::response(
                '<script id="__NEXT_DATA__" type="application/json">{"buildId":"currentBuild"}</script>',
            ),
            'marketplace.logi.com/_next/data/currentBuild/profiles/en/4.json' => Http::response([
                'pageProps' => [
                    'initialState' => [
                        'profileList' => ['profiles' => $standalone],
                        'profileGroupList' => [
                            'profileGroups' => array_map(
                                fn (string $group): array => ['name' => $group],
                                array_keys($groups),
                            ),
                        ],
                        'profileCategoryList' => [
                            'profileCategories' => [
                                ['id' => 0, 'name' => 'All'],
                                ['id' => 41, 'name' => 'Audio'],
                                ['id' => 43, 'name' => 'Creativity'],
                                ['id' => 768, 'name' => 'Video'],
                            ],
                        ],
                    ],
                ],
            ]),
        ];

        foreach ($groups as $group => $profiles) {
            $fakes["marketplace.logi.com/_next/data/currentBuild/profilegroup/{$group}/en.json*"] = Http::response([
                'pageProps' => ['initialState' => ['profileGroupDetails' => ['profiles' => $profiles]]],
            ]);
        }

        Http::fake($fakes);
    }

    private function fakeProfileDetails(string $name): void
    {
        Http::fake([
            "marketplace.logi.com/_next/data/currentBuild/profile/{$name}/en.json*" => Http::response([
                'pageProps' => ['initialState' => ['profileDetails' => ['profile' => [
                    'name' => $name,
                    'author' => ['name' => 'Logitech'],
                    'packages' => [[
                        'name' => $name,
                        'version' => '1.2.0',
                        'package' => "https://example.com/{$name}.lp5",
                        'supportedDevice' => [
                            'displayName' => 'MX Creative Console',
                            'manifest' => ['huge' => 'device manifest'],
                        ],
                        'supportedPlatforms' => [['displayName' => 'Windows']],
                        'preview' => ['image' => 'data:image/png;base64,AAAA'],
                    ]],
                ]]]],
            ]),
        ]);
    }
}
