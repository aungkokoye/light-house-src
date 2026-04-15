<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminSiteController extends Controller
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

        $query = Site::orderBy($by, $dir);

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

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255|unique:sites,name',
            'description' => 'nullable|string',
            'address'     => 'nullable|string',
            'phone'       => 'nullable|string|max:50',
        ]);

        $site = Site::create([...$data, 'created_by' => Auth::id()]);

        return response()->json($site->load(['createdBy:id,name,email']), 201);
    }

    public function show(Site $site): JsonResponse
    {
        return response()->json($site->load('createdBy'));
    }

    public function update(Request $request, Site $site): JsonResponse
    {
        $data = $request->validate([
            'name'        => "required|string|max:255|unique:sites,name,{$site->id}",
            'description' => 'nullable|string',
            'address'     => 'nullable|string',
            'phone'       => 'nullable|string|max:50',
        ]);

        $site->update($data);

        return response()->json($site->load('createdBy'));
    }

    public function destroy(Site $site): JsonResponse
    {
        $site->delete();

        return response()->json(['message' => 'Site deleted successfully.']);
    }
}