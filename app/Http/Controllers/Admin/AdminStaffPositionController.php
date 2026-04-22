<?php

namespace App\Http\Controllers\Admin;

use App\Concerns\AuditableCrud;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStaffPositionRequest;
use App\Http\Requests\Admin\UpdateStaffPositionRequest;
use App\Models\StaffPosition;
use App\Services\StaffPositionManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminStaffPositionController extends Controller
{
    use AuditableCrud;

    public function __construct(private readonly StaffPositionManager $staffPositionManager) {}

    public function all(): JsonResponse
    {
        return response()->json($this->staffPositionManager->all());
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 20);

        return response()->json($this->staffPositionManager->list($request, $perPage));
    }

    public function store(StoreStaffPositionRequest $request): JsonResponse
    {
        $staffPosition = $this->staffPositionManager->create($request->validated());
        $this->auditCreated($staffPosition);

        return response()->json($staffPosition, 201);
    }

    public function show(StaffPosition $staffPosition): JsonResponse
    {
        return response()->json($this->staffPositionManager->show($staffPosition));
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
        $this->auditDeleted($staffPosition);
        $this->staffPositionManager->delete($staffPosition);

        return response()->json(['message' => 'Staff position deleted successfully.']);
    }
}