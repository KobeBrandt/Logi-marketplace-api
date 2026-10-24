<?php

namespace App\Http\Controllers;

use App\Http\Requests\IconPackIndexRequest;
use App\Http\Resources\IconPackResource;
use App\Services\MarketplacePluginService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class IconPackController extends Controller
{
    public function __construct(
        private readonly MarketplacePluginService $marketplace,
    ) {}

    public function index(IconPackIndexRequest $request): AnonymousResourceCollection
    {
        $iconPacks = $this->marketplace->iconPacks($request->filters());
        $page = $request->page();
        $perPage = $request->perPage();

        $paginator = new LengthAwarePaginator(
            array_slice($iconPacks, ($page - 1) * $perPage, $perPage),
            count($iconPacks),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()],
        );

        return IconPackResource::collection($paginator);
    }

    public function show(string $name): IconPackResource
    {
        $iconPack = $this->marketplace->findIconPack($name);

        if ($iconPack === null) {
            abort(404, 'Icon pack not found.');
        }

        return new IconPackResource($iconPack);
    }
}
