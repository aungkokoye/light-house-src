<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class AdminPermissionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $sortBy  = in_array($request->sort_by, ['id', 'name', 'created_at', 'updated_at']) ? $request->sort_by : 'id';
        $sortDir = $request->sort_dir === 'desc' ? 'desc' : 'asc';

        $counts = DB::table('model_has_permissions')
            ->selectRaw('permission_id, count(*) as total')
            ->groupBy('permission_id')
            ->pluck('total', 'permission_id');

        $permissions = Permission::orderBy($sortBy, $sortDir)
            ->get()
            ->each(fn($p) => $p->users_count = $counts[$p->id] ?? 0);

        return response()->json($permissions);
    }

    public function show(Permission $permission): JsonResponse
    {
        $permission->users_count = DB::table('model_has_permissions')
            ->where('permission_id', $permission->id)
            ->count();
        $creator = $permission->created_by ? User::find($permission->created_by) : null;
        $permission->created_by_name  = $creator?->name  ?? null;
        $permission->created_by_email = $creator?->email ?? null;

        return response()->json($permission);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
        ]);

        $permission = Permission::create(['name' => $request->name, 'guard_name' => 'web', 'created_by' => Auth::id()]);
        $permission->users_count = 0;

        return response()->json($permission, 201);
    }

    public function update(Request $request, Permission $permission): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,' . $permission->id],
        ]);

        $permission->update(['name' => $request->name]);
        $permission->users_count = DB::table('model_has_permissions')
            ->where('permission_id', $permission->id)
            ->count();

        return response()->json($permission);
    }

    public function destroy(Permission $permission): JsonResponse
    {
        DB::table('model_has_permissions')->where('permission_id', $permission->id)->delete();
        $permission->delete();

        return response()->json(['message' => 'Permission deleted.']);
    }
}