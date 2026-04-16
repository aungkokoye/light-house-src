<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Services\RoleManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AdminRoleController extends Controller
{
    public function __construct(private readonly RoleManager $roleManager) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 20);

        return response()->json($this->roleManager->list($request, $perPage));
    }

    public function all(): JsonResponse
    {
        return response()->json($this->roleManager->all());
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        return response()->json($this->roleManager->create($request->name), 201);
    }

    public function show(Role $role): JsonResponse
    {
        return response()->json($this->roleManager->show($role));
    }

    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        return response()->json($this->roleManager->update($role, $request->name));
    }

    public function destroy(Role $role): JsonResponse
    {
        $this->roleManager->delete($role);

        return response()->json(['message' => 'Role deleted successfully.']);
    }
}
