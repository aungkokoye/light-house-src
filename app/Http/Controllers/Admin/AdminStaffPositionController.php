<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStaffPositionRequest;
use App\Http\Requests\Admin\UpdateStaffPositionRequest;
use App\Models\StaffPosition;
use App\Services\StaffPositionManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminStaffPositionController extends Controller
{
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
        return response()->json($this->staffPositionManager->create($request->validated()), 201);
    }

    public function show(StaffPosition $staffPosition): JsonResponse
    {
        return response()->json($this->staffPositionManager->show($staffPosition));
    }

    public function update(UpdateStaffPositionRequest $request, StaffPosition $staffPosition): JsonResponse
    {
        return response()->json($this->staffPositionManager->update($staffPosition, $request->validated()));
    }

    public function destroy(StaffPosition $staffPosition): JsonResponse
    {
        $this->staffPositionManager->delete($staffPosition);

        return response()->json(['message' => 'Staff position deleted successfully.']);
    }
}
