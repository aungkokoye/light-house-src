<?php

namespace App\Http\Controllers\Admin;

use App\Concerns\AuditableCrud;
use App\Concerns\HasAbilities;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePermissionRequest;
use App\Http\Requests\Admin\UpdatePermissionRequest;
use App\Services\PermissionManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class AdminPermissionController extends Controller
{
    use AuditableCrud, HasAbilities;

    public function __construct(private readonly PermissionManager $permissionManager) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 20);
        $list    = $this->permissionManager->list($request, $perPage);

        return response()->json(array_merge($list->toArray(), [
            'can' => $this->listAbilities(Permission::class),
        ]));
    }

    public function all(): JsonResponse
    {
        return response()->json($this->permissionManager->all());
    }

    public function store(StorePermissionRequest $request): JsonResponse
    {
        $permission = $this->permissionManager->create($request->name);
        $this->auditCreated($permission);

        return response()->json($permission, 201);
    }

    public function show(Permission $permission): JsonResponse
    {
        return response()->json(array_merge($this->permissionManager->show($permission)->toArray(), [
            'can' => $this->resourceAbilities($permission),
        ]));
    }

    public function update(UpdatePermissionRequest $request, Permission $permission): JsonResponse
    {
        $oldValues = $this->filterAuditValues($permission->getAttributes());
        $updated   = $this->permissionManager->update($permission, $request->name);
        $this->auditUpdated($updated, $oldValues);

        return response()->json($updated);
    }

    public function destroy(Permission $permission): JsonResponse
    {
        $this->auditDeleted($permission);
        $this->permissionManager->delete($permission);

        return response()->json(['message' => 'Permission deleted.']);
    }
}