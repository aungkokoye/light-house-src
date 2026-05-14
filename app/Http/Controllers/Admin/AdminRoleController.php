<?php

namespace App\Http\Controllers\Admin;

use App\Concerns\AuditableCrud;
use App\Concerns\HasAbilities;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Services\RoleManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AdminRoleController extends Controller
{
    use AuditableCrud, HasAbilities;

    public function __construct(private readonly RoleManager $roleManager) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 20);
        $list    = $this->roleManager->list($request, $perPage);

        return response()->json(array_merge($list->toArray(), [
            'can' => $this->listAbilities(Role::class),
        ]));
    }

    public function all(): JsonResponse
    {
        return response()->json($this->roleManager->all());
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = $this->roleManager->create($request->name);
        $this->auditCreated($role);

        return response()->json($role, 201);
    }

    public function show(Role $role): JsonResponse
    {
        return response()->json(array_merge($this->roleManager->show($role)->toArray(), [
            'can' => $this->resourceAbilities($role),
        ]));
    }

    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $oldValues = $this->filterAuditValues($role->getAttributes());
        $updated   = $this->roleManager->update($role, $request->name);
        $this->auditUpdated($updated, $oldValues);

        return response()->json($updated);
    }

    public function destroy(Role $role): JsonResponse
    {
        $this->auditDeleted($role);
        $this->roleManager->delete($role);

        return response()->json(['message' => 'Role deleted successfully.']);
    }
}
