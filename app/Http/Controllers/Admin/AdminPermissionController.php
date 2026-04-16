<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePermissionRequest;
use App\Http\Requests\Admin\UpdatePermissionRequest;
use App\Services\PermissionManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class AdminPermissionController extends Controller
{
    public function __construct(private readonly PermissionManager $permissionManager) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->permissionManager->list($request));
    }

    public function store(StorePermissionRequest $request): JsonResponse
    {
        return response()->json($this->permissionManager->create($request->name), 201);
    }

    public function show(Permission $permission): JsonResponse
    {
        return response()->json($this->permissionManager->show($permission));
    }

    public function update(UpdatePermissionRequest $request, Permission $permission): JsonResponse
    {
        return response()->json($this->permissionManager->update($permission, $request->name));
    }

    public function destroy(Permission $permission): JsonResponse
    {
        $this->permissionManager->delete($permission);

        return response()->json(['message' => 'Permission deleted.']);
    }
}
