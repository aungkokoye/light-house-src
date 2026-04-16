<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSiteRequest;
use App\Http\Requests\Admin\UpdateSiteRequest;
use App\Models\Site;
use App\Services\SiteManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminSiteController extends Controller
{
    public function __construct(private readonly SiteManager $siteManager) {}

    public function all(): JsonResponse
    {
        return response()->json($this->siteManager->all());
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 20);

        return response()->json($this->siteManager->list($request, $perPage));
    }

    public function store(StoreSiteRequest $request): JsonResponse
    {
        return response()->json($this->siteManager->create($request->validated()), 201);
    }

    public function show(Site $site): JsonResponse
    {
        return response()->json($this->siteManager->show($site));
    }

    public function update(UpdateSiteRequest $request, Site $site): JsonResponse
    {
        return response()->json($this->siteManager->update($site, $request->validated()));
    }

    public function destroy(Site $site): JsonResponse
    {
        $this->siteManager->delete($site);

        return response()->json(['message' => 'Site deleted successfully.']);
    }
}
