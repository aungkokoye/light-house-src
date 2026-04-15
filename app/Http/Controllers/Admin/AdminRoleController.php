<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class AdminRoleController extends Controller
{
    const array SORTABLE     = ['id', 'name', 'created_at', 'updated_at'];
    const array PER_PAGE_LIST = [10, 20, 30, 40, 50];
    const int DEFAULT_PER_PAGE = 20;

    public function index(Request $request): JsonResponse
    {
        $by  = in_array($request->input('sort_by'), self::SORTABLE) ? $request->input('sort_by') : 'updated_at';
        $dir = $request->input('sort_dir') === 'asc' ? 'asc' : 'desc';
        $perPage = in_array((int) $request->input('per_page'), self::PER_PAGE_LIST)
            ? (int) $request->input('per_page')
            : self::DEFAULT_PER_PAGE;

        $query = Role::orderBy($by, $dir);

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $paginated = $query->paginate($perPage);

        $counts = DB::table('model_has_roles')
            ->selectRaw('role_id, count(*) as total')
            ->whereIn('role_id', $paginated->pluck('id'))
            ->groupBy('role_id')
            ->pluck('total', 'role_id');

        $paginated->each(fn($role) => $role->users_count = $counts[$role->id] ?? 0);

        return response()->json($paginated);
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = Role::create(['name' => $request->name, 'guard_name' => 'web', 'created_by' => Auth::id()]);

        return response()->json($role, 201);
    }

    public function show(Role $role): JsonResponse
    {
        $role->users_count = DB::table('model_has_roles')->where('role_id', $role->id)->count();
        $creator = $role->created_by ? User::find($role->created_by, ['id', 'name', 'email']) : null;
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
        DB::table('model_has_roles')->where('role_id', $role->id)->delete();
        $role->delete();

        return response()->json(['message' => 'Role deleted successfully.']);
    }
}