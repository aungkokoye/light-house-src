<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class AdminRoleController extends Controller
{
    public function index(): JsonResponse
    {
        $sortable = ['id', 'name', 'created_at', 'updated_at'];
        $by  = in_array(request('sort_by'), $sortable) ? request('sort_by') : 'updated_at';
        $dir = request('sort_dir') === 'asc' ? 'asc' : 'desc';

        $roles = Role::orderBy($by, $dir)
            ->get()
            ->each(fn($role) => $role->users_count = DB::table('model_has_roles')
                ->where('role_id', $role->id)
                ->count()
            );

        return response()->json($roles);
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = Role::create(['name' => $request->name, 'guard_name' => 'web', 'created_by' => Auth::id()]);

        return response()->json($role, 201);
    }

    public function show(Role $role): JsonResponse
    {
        $role->users_count = DB::table('model_has_roles')->where('role_id', $role->id)->count();
        $creator = $role->created_by ? User::find($role->created_by) : null;
        $role->created_by_name  = $creator?->name  ?? config('app.default_creator_name');
        $role->created_by_email = $creator?->email ?? config('app.default_creator_email');

        return response()->json($role);
    }

    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $role->update(['name' => $request->name, 'created_by' => Auth::id()]);
        $role->users_count = DB::table('model_has_roles')->where('role_id', $role->id)->count();

        return response()->json($role);
    }

    public function destroy(Role $role): JsonResponse
    {
        $role->delete();

        return response()->json(['message' => 'Role deleted successfully.']);
    }
}