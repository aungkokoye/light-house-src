<?php

namespace App\Http\Controllers\Admin;

use App\Concerns\AuditableCrud;
use App\Concerns\HasAbilities;
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
    use AuditableCrud, HasAbilities;

    public function __construct(private readonly StaffRoleManager $staffRoleManager) {}

    public function index(Request $request, User $user): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 20);
        $list    = $this->staffRoleManager->list($request, $user, $perPage);

        return response()->json(array_merge($list->toArray(), [
            'can' => $this->listAbilities(StaffRole::class),
        ]));
    }

    public function store(StoreStaffRoleRequest $request, User $user): JsonResponse
    {
        $staffRole = $this->staffRoleManager->create($user, $request->validated());
        $staffRole->load(['staffProfile.user:id,name', 'position:id,name', 'site:id,name', 'createdBy:id,name']);
        $this->auditCreated($staffRole, $this->snapshot($staffRole));

        return response()->json($staffRole, 201);
    }

    public function show(User $user, StaffRole $staffRole): JsonResponse
    {
        $this->staffRoleManager->authorize($user, $staffRole);

        return response()->json(array_merge($this->staffRoleManager->show($staffRole)->toArray(), [
            'can' => $this->resourceAbilities($staffRole),
        ]));
    }

    public function update(UpdateStaffRoleRequest $request, User $user, StaffRole $staffRole): JsonResponse
    {
        $this->staffRoleManager->authorize($user, $staffRole);
        $staffRole->load(['staffProfile.user:id,name', 'position:id,name', 'site:id,name', 'createdBy:id,name']);
        $oldValues = $this->snapshot($staffRole);

        $updated = $this->staffRoleManager->update($staffRole, $request->validated());
        $updated->load(['staffProfile.user:id,name', 'position:id,name', 'site:id,name', 'createdBy:id,name']);
        $this->auditUpdated($updated, $oldValues, $this->snapshot($updated));

        return response()->json($updated);
    }

    public function destroy(User $user, StaffRole $staffRole): JsonResponse
    {
        $this->staffRoleManager->authorize($user, $staffRole);
        $staffRole->load(['staffProfile.user:id,name', 'position:id,name', 'site:id,name', 'createdBy:id,name']);
        $this->auditDeleted($staffRole, $this->snapshot($staffRole));
        $this->staffRoleManager->delete($staffRole);

        return response()->json(['message' => 'Staff role deleted successfully.']);
    }

    private function snapshot(StaffRole $staffRole): array
    {
        $attrs = $this->filterAuditValues($staffRole->getAttributes());

        unset($attrs['staff_profile_id'], $attrs['staff_position_id'], $attrs['site_id'], $attrs['created_by']);

        return array_merge($attrs, [
            'staff_profile'  => ['id' => $staffRole->staffProfile?->id, 'full_name' => $staffRole->staffProfile?->full_name],
            'user'           => ['id' => $staffRole->staffProfile?->user?->id,  'name' => $staffRole->staffProfile?->user?->name],
            'staff_position' => ['id' => $staffRole->position?->id,             'name' => $staffRole->position?->name],
            'site'           => ['id' => $staffRole->site?->id,                 'name' => $staffRole->site?->name],
            'created_by'     => ['id' => $staffRole->createdBy?->id,            'name' => $staffRole->createdBy?->name],
        ]);
    }
}
