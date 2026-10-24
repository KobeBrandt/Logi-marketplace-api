<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileIndexRequest;
use App\Http\Resources\ProfileResource;
use App\Services\MarketplacePluginService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProfileController extends Controller
{
    public function __construct(
        private readonly MarketplacePluginService $marketplace,
    ) {}

    public function index(ProfileIndexRequest $request): AnonymousResourceCollection
    {
        $profiles = $this->marketplace->profiles($request->filters());
        $page = $request->page();
        $perPage = $request->perPage();

        $paginator = new LengthAwarePaginator(
            array_slice($profiles, ($page - 1) * $perPage, $perPage),
            count($profiles),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()],
        );

        return ProfileResource::collection($paginator);
    }

    public function show(string $name): ProfileResource
    {
        $profile = $this->marketplace->findProfile($name);

        if ($profile === null) {
            abort(404, 'Profile not found.');
        }

        return new ProfileResource($profile);
    }
}
