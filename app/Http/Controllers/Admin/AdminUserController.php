<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Services\EmailManager;
use App\Services\UserManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    const int DEFAULT_PER_PAGE = 20;
    const array PER_PAGE_LIST = [10, 20, 30, 40, 50];

    public function __construct(
        private readonly UserManager $userManager,
        private readonly EmailManager $emailManager,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = in_array((int) $request->input('per_page'), self::PER_PAGE_LIST)
            ? (int) $request->input('per_page')
            : self::DEFAULT_PER_PAGE;

        return response()->json($this->userManager->list($request, $perPage));
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userManager->create(
            name: $request->name,
            email: $request->email,
            password: $request->password,
            role: $request->role,
            activated: $request->boolean('activated', true),
            emailVerified: $request->boolean('email_verified'),
        );

        if (! $request->boolean('email_verified')) {
            $this->emailManager->sendVerificationEmail($user);
        }

        return response()->json($user->load('roles'), 201);
    }

    public function show(User $user): JsonResponse
    {
        $user->load('roles');
        $creator = $user->created_by ? User::find($user->created_by) : null;
        $user->created_by_name  = $creator?->name  ?? config('app.default_creator_name');
        $user->created_by_email = $creator?->email ?? config('app.default_creator_email');

        return response()->json($user);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $user = $this->userManager->update(
            user: $user,
            name: $request->name,
            email: $request->email,
            role: $request->role,
            activated: $request->boolean('activated', true),
            emailVerified: $request->boolean('email_verified'),
            password: $request->filled('password') ? $request->password : null,
        );

        return response()->json($user);
    }

    public function destroy(User $user): JsonResponse
    {
        $this->userManager->delete($user);

        return response()->json(['message' => 'User deleted successfully.']);
    }
}