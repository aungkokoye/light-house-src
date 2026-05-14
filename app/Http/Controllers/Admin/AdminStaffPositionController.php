<?php

namespace App\Http\Controllers\Admin;

use App\Concerns\AuditableCrud;
use App\Concerns\HasAbilities;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStaffPositionRequest;
use App\Http\Requests\Admin\UpdateStaffPositionRequest;
use App\Models\StaffPosition;
use App\Services\StaffPositionManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminStaffPositionController extends Controller
{
    use AuditableCrud, HasAbilities;

    public function __construct(private readonly StaffPositionManager $staffPositionManager) {}

    public function all(): JsonResponse
    {
        return response()->json($this->staffPositionManager->all());
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 20);
        $list    = $this->staffPositionManager->list($request, $perPage);

        return response()->json(array_merge($list->toArray(), [
            'can' => $this->listAbilities(StaffPosition::class),
        ]));
    }

    public function store(StoreStaffPositionRequest $request): JsonResponse
    {
        $staffPosition = $this->staffPositionManager->create($request->validated());
        $this->auditCreated($staffPosition);

        return response()->json($staffPosition, 201);
    }

    public function show(StaffPosition $staffPosition): JsonResponse
    {
        return response()->json(array_merge($this->staffPositionManager->show($staffPosition)->toArray(), [
            'can' => $this->resourceAbilities($staffPosition),
        ]));
    }

    public function update(UpdateStaffPositionRequest $request, StaffPosition $staffPosition): JsonResponse
    {
        $oldValues = $this->filterAuditValues($staffPosition->getAttributes());
        $updated   = $this->staffPositionManager->update($staffPosition, $request->validated());
        $this->auditUpdated($updated, $oldValues);

        return response()->json($updated);
    }

    public function destroy(StaffPosition $staffPosition): JsonResponse
    {
        $snapshot = $this->filterAuditValues($staffPosition->getAttributes());

        try {
            $this->staffPositionManager->delete($staffPosition);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json(['message' => 'Cannot delete this position because it is assigned to one or more staff members.'], 422);
            }
            throw $e;
        }

        $this->auditDeleted($staffPosition, $snapshot);

        return response()->json(['message' => 'Staff position deleted successfully.']);
    }
}