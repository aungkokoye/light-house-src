<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffPosition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminStaffPositionController extends Controller
{
    const int DEFAULT_PER_PAGE = 20;
    const array PER_PAGE_LIST  = [10, 20, 30, 40, 50];
    const array SORTABLE       = ['id', 'name', 'created_at', 'updated_at'];

    public function index(Request $request): JsonResponse
    {
        $perPage = in_array((int) $request->input('per_page'), self::PER_PAGE_LIST)
            ? (int) $request->input('per_page')
            : self::DEFAULT_PER_PAGE;

        $by  = in_array($request->input('sort_by'), self::SORTABLE) ? $request->input('sort_by') : 'name';
        $dir = $request->input('sort_dir') === 'desc' ? 'desc' : 'asc';

        $query = StaffPosition::orderBy($by, $dir);

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($from = $request->input('created_from')) {
            if (strtotime($from)) {
                $query->whereDate('created_at', '>=', $from);
            }
        }

        if ($to = $request->input('created_to')) {
            if (strtotime($to)) {
                $query->whereDate('created_at', '<=', $to);
            }
        }

        return response()->json($query->paginate($perPage));
    }

    /** Simple flat list for dropdowns. */
    public function all(): JsonResponse
    {
        return response()->json(StaffPosition::orderBy('name')->get(['id', 'name']));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255|unique:staff_positions,name',
            'description' => 'nullable|string|max:10000',
        ]);

        $position = StaffPosition::create([...$data, 'created_by' => Auth::id()]);

        return response()->json($position->load(['createdBy:id,name,email']), 201);
    }

    public function show(StaffPosition $staffPosition): JsonResponse
    {
        return response()->json($staffPosition->load(['createdBy:id,name,email']));
    }

    public function update(Request $request, StaffPosition $staffPosition): JsonResponse
    {
        $data = $request->validate([
            'name'        => "required|string|max:255|unique:staff_positions,name,{$staffPosition->id}",
            'description' => 'nullable|string|max:10000',
        ]);

        $staffPosition->update($data);

        return response()->json($staffPosition->load(['createdBy:id,name,email']));
    }

    public function destroy(StaffPosition $staffPosition): JsonResponse
    {
        $staffPosition->delete();

        return response()->json(['message' => 'Staff position deleted successfully.']);
    }
}
