<?php

namespace App\Http\Controllers\Admin;

use App\Concerns\AuditableCrud;
use App\Concerns\HasAbilities;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSiteRequest;
use App\Http\Requests\Admin\UpdateSiteRequest;
use App\Models\Site;
use App\Services\SiteManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminSiteController extends Controller
{
    use AuditableCrud, HasAbilities;

    public function __construct(private readonly SiteManager $siteManager) {}

    public function all(): JsonResponse
    {
        return response()->json($this->siteManager->all());
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 20);
        $list    = $this->siteManager->list($request, $perPage);

        return response()->json(array_merge($list->toArray(), [
            'can' => $this->listAbilities(Site::class),
        ]));
    }

    public function store(StoreSiteRequest $request): JsonResponse
    {
        $site = $this->siteManager->create($request->validated());
        $this->auditCreated($site);

        return response()->json($site, 201);
    }

    public function show(Site $site): JsonResponse
    {
        return response()->json(array_merge($this->siteManager->show($site)->toArray(), [
            'can' => $this->resourceAbilities($site),
        ]));
    }

    public function update(UpdateSiteRequest $request, Site $site): JsonResponse
    {
        $oldValues = $this->filterAuditValues($site->getAttributes());
        $updated   = $this->siteManager->update($site, $request->validated());
        $this->auditUpdated($updated, $oldValues);

        return response()->json($updated);
    }

    public function destroy(Site $site): JsonResponse
    {
        $this->auditDeleted($site);
        $this->siteManager->delete($site);

        return response()->json(['message' => 'Site deleted successfully.']);
    }
}