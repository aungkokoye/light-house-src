<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStaffRoleRequest;
use App\Http\Requests\Admin\UpdateStaffRoleRequest;
use App\Models\StaffRole;
use App\Models\User;
use App\Services\StaffRoleManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminStaffRoleController extends Controller
{
    public function __construct(private readonly StaffRoleManager $staffRoleManager) {}

    public function index(Request $request, User $user): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 20);

        return response()->json($this->staffRoleManager->list($request, $user, $perPage));
    }

    public function store(StoreStaffRoleRequest $request, User $user): JsonResponse
    {
        return response()->json($this->staffRoleManager->create($user, $request->validated()), 201);
    }

    public function show(User $user, StaffRole $staffRole): JsonResponse
    {
        $this->staffRoleManager->authorize($user, $staffRole);

        return response()->json($this->staffRoleManager->show($staffRole));
    }

    public function update(UpdateStaffRoleRequest $request, User $user, StaffRole $staffRole): JsonResponse
    {
        $this->staffRoleManager->authorize($user, $staffRole);

        return response()->json($this->staffRoleManager->update($staffRole, $request->validated()));
    }

    public function destroy(User $user, StaffRole $staffRole): JsonResponse
    {
        $this->staffRoleManager->authorize($user, $staffRole);
        $this->staffRoleManager->delete($staffRole);

        return response()->json(['message' => 'Staff role deleted successfully.']);
    }
}
