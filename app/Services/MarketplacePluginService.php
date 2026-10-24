<?php

namespace App\Services;

use App\Exceptions\MarketplaceException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MarketplacePluginService
{
    public function list(array $filters = []): array
    {
        return $this->filter($this->plugins(), $filters);
    }

    public function find(string $name): ?array
    {
        foreach ($this->plugins() as $plugin) {
            if (strcasecmp($plugin['name'], $name) === 0) {
                return array_merge($plugin, $this->details($plugin['name']));
            }
        }

        return null;
    }

    public function iconPacks(array $filters = []): array
    {
        return $this->filter($this->allIconPacks(), $filters);
    }

    public function findIconPack(string $name): ?array
    {
        foreach ($this->allIconPacks() as $iconPack) {
            if (strcasecmp($iconPack['name'], $name) === 0) {
                return array_merge($iconPack, $this->iconPackDetails($iconPack['name']));
            }
        }

        return null;
    }

    public function profiles(array $filters = []): array
    {
        return $this->filter($this->allProfiles(), $filters);
    }

    public function findProfile(string $name): ?array
    {
        foreach ($this->allProfiles() as $profile) {
            if (strcasecmp($profile['name'], $name) === 0) {
                return array_merge($profile, $this->profileDetails($profile['name']));
            }
        }

        return null;
    }

    /**
     * Keep only the items (plugins, icon packs or profiles) that match every given filter.
     */
    private function filter(array $items, array $filters): array
    {
        return array_values(array_filter($items, function (array $item) use ($filters): bool {
            $search = $filters['search'] ?? null;

            if ($search !== null && $search !== '') {
                $name = (string) ($item['name'] ?? '');
                $displayName = (string) ($item['displayName'] ?? '');

                if (stripos($name, $search) === false && stripos($displayName, $search) === false) {
                    return false;
                }
            }

            if (($filters['platform'] ?? null) !== null
                && ! in_array($filters['platform'], $item['platforms'] ?? [], true)) {
                return false;
            }

            if (($filters['device'] ?? null) !== null
                && ! in_array($filters['device'], $item['devices'] ?? [], true)) {
                return false;
            }

            return ($filters['category'] ?? null) === null
                || in_array($filters['category'], $item['categories'] ?? [], true);
        }));
    }

    private function plugins(): array
    {
        return Cache::remember('marketplace.plugins', $this->cacheTtl(), function (): array {
            $data = $this->getNextData('/_next/data/%s/plugins/en/4.json');
            $rawPlugins = $data['pageProps']['initialState']['pluginList']['plugins'] ?? null;

            if (! is_array($rawPlugins) || $rawPlugins === []) {
                throw new MarketplaceException('No plugins found in the marketplace response.');
            }

            $downloads = $this->getJson($this->marketplaceUrl('/api/downloads'))['plugins'] ?? [];

            return array_map(fn (array $plugin): array => $this->mapPlugin($plugin, $downloads), $rawPlugins);
        });
    }

    private function details(string $name): array
    {
        return Cache::remember('marketplace.plugin.details.'.sha1($name), $this->cacheTtl(), function () use ($name): array {
            $data = $this->getNextData(
                '/_next/data/%s/plugin/%s/en.json',
                [rawurlencode($name)],
                ['language' => 'en', 'pluginName' => $name],
            );
            $plugin = $data['pageProps']['initialState']['pluginDetails']['data'] ?? null;

            if (! is_array($plugin)) {
                throw new MarketplaceException('Detailed plugin data not found.');
            }

            return [
                'author' => [
                    'name' => $plugin['author']['name'] ?? null,
                    'supportUrl' => $plugin['author']['supportUrl'] ?? null,
                ],
                'homeUrl' => $plugin['homeUrl'] ?? null,
                'requirements' => array_values(array_map(
                    fn (array $faq): mixed => $faq['answer'] ?? null,
                    array_filter(
                        $plugin['faqs'] ?? [],
                        fn (array $faq): bool => stripos((string) ($faq['question'] ?? ''), 'requirement') !== false,
                    ),
                )),
                'versions' => array_map(fn (array $package): array => [
                    'version' => $package['version'] ?? null,
                    'fileUrl' => $package['fileUrl'] ?? null,
                    'minLPSVersion' => $package['minLPSVersion'] ?? null,
                    'platforms' => array_map(
                        fn (array $platform): ?string => $platform['displayName']
                            ?? $platform['platformDisplay']
                            ?? null,
                        $package['supportedPlatforms'] ?? [],
                    ),
                ], $plugin['packages'] ?? []),
                'capabilities' => $plugin['capabilities'] ?? [],
                'supportedFeatures' => $plugin['supportedFeatures'] ?? [],
            ];
        });
    }

    private function mapPlugin(array $plugin, array $downloads): array
    {
        $name = $plugin['name'] ?? null;

        return [
            'id' => $plugin['id'] ?? null,
            'name' => $name,
            'displayName' => $plugin['displayName'] ?? null,
            'description' => $plugin['description'] ?? null,
            'categories' => array_column($plugin['categories'] ?? [], 'name'),
            'icon' => $plugin['icon'] ?? null,
            'versions' => array_column($plugin['packages'] ?? [], 'version'),
            'platforms' => $this->platformNames($plugin['packages'] ?? []),
            'firstPublicAt' => $plugin['firstPublicAt'] ?? null,
            'downloads' => $downloads[$name]['downloads']
                ?? $downloads[$plugin['displayName'] ?? '']['downloads']
                ?? 0,
        ];
    }

    private function allIconPacks(): array
    {
        return Cache::remember('marketplace.icon_packs', $this->cacheTtl(), function (): array {
            $data = $this->getNextData('/_next/data/%s/icons/en/4.json');
            $state = $data['pageProps']['initialState'] ?? [];
            $rawIconPacks = $state['iconList']['icons'] ?? null;

            if (! is_array($rawIconPacks) || $rawIconPacks === []) {
                throw new MarketplaceException('No icon packs found in the marketplace response.');
            }

            $categoryNames = array_column($state['iconCategoryList']['iconCategories'] ?? [], 'name', 'id');

            return array_map(fn (array $iconPack): array => $this->mapIconPack($iconPack, $categoryNames), $rawIconPacks);
        });
    }

    private function iconPackDetails(string $name): array
    {
        return Cache::remember('marketplace.icon_pack.details.'.sha1($name), $this->cacheTtl(), function () use ($name): array {
            $data = $this->getNextData(
                '/_next/data/%s/icon/%s/en.json',
                [rawurlencode($name)],
                ['language' => 'en', 'iconName' => $name],
            );
            $iconPack = $data['pageProps']['initialState']['iconDetails']['icon'] ?? null;

            if (! is_array($iconPack)) {
                throw new MarketplaceException('Detailed icon pack data not found.');
            }

            return [
                'description' => $iconPack['description'] ?? null,
                'copyright' => $iconPack['copyright'] ?? null,
                'author' => [
                    'name' => $iconPack['author']['name'] ?? null,
                ],
                'package' => $iconPack['package'] ?? null,
                'previewImages' => array_map(fn (array $image): array => [
                    'url' => $image['url'] ?? null,
                    'tooltip' => $image['tooltip'] ?? null,
                ], $iconPack['previewImages'] ?? []),
            ];
        });
    }

    private function mapIconPack(array $iconPack, array $categoryNames): array
    {
        $categories = [];

        foreach ($iconPack['categoryIds'] ?? [] as $categoryId) {
            // Category 0 is the marketplace's "All" tab, not a real category.
            if ($categoryId !== 0 && isset($categoryNames[$categoryId])) {
                $categories[] = $categoryNames[$categoryId];
            }
        }

        return [
            'id' => $iconPack['id'] ?? null,
            'name' => $iconPack['name'] ?? null,
            'displayName' => $iconPack['displayName'] ?? null,
            'categories' => $categories,
            'icon' => $iconPack['icon'] ?? null,
            'cardIcon' => $iconPack['cardIcon'] ?? null,
            'firstPublicAt' => $iconPack['firstPublicAt'] ?? null,
        ];
    }

    /**
     * The marketplace lists a few standalone profiles, but most profiles live
     * inside profile groups (one per app, e.g. Photoshop). We merge both.
     */
    private function allProfiles(): array
    {
        return Cache::remember('marketplace.profiles', $this->cacheTtl(), function (): array {
            $state = $this->getNextData('/_next/data/%s/profiles/en/4.json')['pageProps']['initialState'] ?? [];

            $categories = [];

            foreach ($state['profileCategoryList']['profileCategories'] ?? [] as $category) {
                if (($category['id'] ?? 0) !== 0) {
                    $categories[$category['id']] = $category['name'] ?? null;
                }
            }

            $profiles = [];

            foreach ($state['profileList']['profiles'] ?? [] as $profile) {
                $profiles[$profile['name']] = $this->mapProfile($profile, null, $categories);
            }

            foreach ($state['profileGroupList']['profileGroups'] ?? [] as $group) {
                $groupName = $group['name'];
                $data = $this->getNextData(
                    '/_next/data/%s/profilegroup/%s/en.json',
                    [rawurlencode($groupName)],
                    ['language' => 'en', 'profileGroupName' => $groupName],
                );

                foreach ($data['pageProps']['initialState']['profileGroupDetails']['profiles'] ?? [] as $profile) {
                    $profiles[$profile['name']] ??= $this->mapProfile($profile, $groupName, $categories);
                }
            }

            if ($profiles === []) {
                throw new MarketplaceException('No profiles found in the marketplace response.');
            }

            return array_values($profiles);
        });
    }

    private function profileDetails(string $name): array
    {
        return Cache::remember('marketplace.profile.details.'.sha1($name), $this->cacheTtl(), function () use ($name): array {
            $data = $this->getNextData(
                '/_next/data/%s/profile/%s/en.json',
                [rawurlencode($name)],
                ['language' => 'en', 'profileName' => $name],
            );
            $profile = $data['pageProps']['initialState']['profileDetails']['profile'] ?? null;

            if (! is_array($profile)) {
                throw new MarketplaceException('Detailed profile data not found.');
            }

            // Packages also contain a huge device manifest and base64 preview
            // images, so we only pick the fields that are useful.
            return [
                'author' => [
                    'name' => $profile['author']['name'] ?? null,
                ],
                'packages' => array_map(fn (array $package): array => [
                    'name' => $package['name'] ?? null,
                    'version' => $package['version'] ?? null,
                    'fileUrl' => $package['package'] ?? null,
                    'device' => $package['supportedDevice']['displayName'] ?? null,
                    'platforms' => $this->platformNames([$package]),
                ], $profile['packages'] ?? []),
            ];
        });
    }

    private function mapProfile(array $profile, ?string $group, array $categories): array
    {
        $devices = [];

        foreach ($profile['packages'] ?? [] as $package) {
            $device = $package['supportedDevice']['displayName'] ?? null;

            if ($device !== null && ! in_array($device, $devices, true)) {
                $devices[] = $device;
            }
        }

        return [
            'id' => $profile['id'] ?? null,
            'name' => $profile['name'] ?? null,
            'displayName' => $profile['displayName'] ?? null,
            'description' => $profile['description'] ?? null,
            'categories' => array_values(array_filter(array_map(
                fn (int|string $id): ?string => $categories[$id] ?? null,
                $profile['categoryIds'] ?? [],
            ))),
            'icon' => $profile['icon'] ?? null,
            'group' => $group,
            'devices' => $devices,
            'platforms' => $this->platformNames($profile['packages'] ?? []),
            'requiredPlugins' => array_column($profile['requiredPlugins'] ?? [], 'name'),
            'firstPublicAt' => $profile['firstPublicAt'] ?? null,
        ];
    }

    /**
     * Unique platform names (e.g. "Windows", "macOS") across the given packages.
     */
    private function platformNames(array $packages): array
    {
        $platforms = [];

        foreach ($packages as $package) {
            foreach ($package['supportedPlatforms'] ?? [] as $platform) {
                $platformName = $platform['displayName'] ?? $platform['platformDisplay'] ?? null;

                if ($platformName !== null && ! in_array($platformName, $platforms, true)) {
                    $platforms[] = $platformName;
                }
            }
        }

        return $platforms;
    }

    /**
     * Fetch a Next.js data route. The marketplace build ID changes on every
     * Logitech deploy, so a 404 means our cached ID is stale: refresh it and retry once.
     */
    private function getNextData(string $path, array $values = [], array $query = []): array
    {
        $url = fn (): string => $this->marketplaceUrl($path, [$this->buildId(), ...$values]);

        try {
            return $this->getJson($url(), $query);
        } catch (MarketplaceException $exception) {
            $previous = $exception->getPrevious();
            $isNotFound = $previous instanceof RequestException && $previous->response->notFound();

            if (! $isNotFound || filled(config('services.logi_marketplace.build_id'))) {
                throw $exception;
            }

            Cache::forget('marketplace.build_id');

            return $this->getJson($url(), $query);
        }
    }

    private function buildId(): string
    {
        $configured = config('services.logi_marketplace.build_id');

        if (filled($configured)) {
            return $configured;
        }

        return Cache::remember('marketplace.build_id', $this->cacheTtl(), function (): string {
            $html = $this->request($this->marketplaceUrl('/plugins/en/4'))->body();

            if (! preg_match('/"buildId"\s*:\s*"([^"]+)"/', $html, $matches)) {
                throw new MarketplaceException('Could not determine the marketplace build ID.');
            }

            return $matches[1];
        });
    }

    private function getJson(string $url, array $query = []): array
    {
        $data = $this->request($url, $query, acceptJson: true)->json();

        if (! is_array($data)) {
            throw new MarketplaceException('Failed to decode marketplace JSON data.');
        }

        return $data;
    }

    private function request(string $url, array $query = [], bool $acceptJson = false): Response
    {
        try {
            return Http::timeout(15)
                ->when($acceptJson, fn ($request) => $request->acceptJson())
                ->get($url, $query)
                ->throw();
        } catch (ConnectionException|RequestException $exception) {
            Log::warning('Marketplace request failed.', [
                'url' => $url,
                'status' => $exception instanceof RequestException ? $exception->response->status() : null,
                'error' => $exception->getMessage(),
            ]);

            throw new MarketplaceException('Failed to fetch marketplace data.', previous: $exception);
        }
    }

    private function cacheTtl(): int
    {
        return (int) config('services.logi_marketplace.cache_ttl');
    }

    private function marketplaceUrl(string $path, array $values = []): string
    {
        return config('services.logi_marketplace.base_url').vsprintf($path, $values);
    }
}
