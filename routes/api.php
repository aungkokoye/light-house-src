<?php

use App\Http\Controllers\Admin\AdminPermissionController;
use App\Http\Controllers\Admin\AdminRoleController;
use App\Http\Controllers\Admin\AdminSiteController;
use App\Http\Controllers\Admin\AdminStaffPositionController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Models\StaffPosition;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\ContactController;
use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

Route::post('/contact', [ContactController::class, 'send'])->middleware(['web']);

Route::post('/register', [AuthController::class, 'register'])->middleware(['web']);
Route::post('/login', [AuthController::class, 'login'])->middleware(['web']);
Route::post('/email/resend', [AuthController::class, 'resendVerification']);
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
Route::post('/reset-password', [PasswordResetController::class, 'reset']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/password', [AuthController::class, 'updatePassword']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/roles',         [AdminRoleController::class, 'index'])  ->can('viewAny', Role::class);
    Route::post('/roles',        [AdminRoleController::class, 'store'])  ->can('create',  Role::class);
    Route::get('/roles/{role}',  [AdminRoleController::class, 'show'])   ->can('view',    'role');
    Route::put('/roles/{role}',  [AdminRoleController::class, 'update']) ->can('update',  'role');
    Route::delete('/roles/{role}',[AdminRoleController::class, 'destroy'])->can('delete',  'role');

    Route::get('/permissions',              [AdminPermissionController::class, 'index'])  ->can('viewAny', Permission::class);
    Route::post('/permissions',             [AdminPermissionController::class, 'store'])  ->can('create',  Permission::class);
    Route::get('/permissions/{permission}', [AdminPermissionController::class, 'show'])   ->can('view',    'permission');
    Route::put('/permissions/{permission}', [AdminPermissionController::class, 'update']) ->can('update',  'permission');
    Route::delete('/permissions/{permission}',[AdminPermissionController::class,'destroy'])->can('delete',  'permission');

    Route::get('/users/types',      [AdminUserController::class, 'types']);
    Route::get('/staff-positions/all',                    [AdminStaffPositionController::class, 'all']);
    Route::get('/staff-positions',                        [AdminStaffPositionController::class, 'index'])  ->can('viewAny', StaffPosition::class);
    Route::post('/staff-positions',                       [AdminStaffPositionController::class, 'store'])  ->can('create',  StaffPosition::class);
    Route::get('/staff-positions/{staffPosition}',        [AdminStaffPositionController::class, 'show'])   ->can('view',    'staffPosition');
    Route::put('/staff-positions/{staffPosition}',        [AdminStaffPositionController::class, 'update']) ->can('update',  'staffPosition');
    Route::delete('/staff-positions/{staffPosition}',     [AdminStaffPositionController::class, 'destroy'])->can('delete',  'staffPosition');
    Route::get('/sites',          [AdminSiteController::class, 'index'])  ->can('viewAny', Site::class);
    Route::post('/sites',         [AdminSiteController::class, 'store'])  ->can('create',  Site::class);
    Route::get('/sites/{site}',   [AdminSiteController::class, 'show'])   ->can('view',    'site');
    Route::put('/sites/{site}',   [AdminSiteController::class, 'update']) ->can('update',  'site');
    Route::delete('/sites/{site}',[AdminSiteController::class, 'destroy'])->can('delete',  'site');
    Route::get('/users',         [AdminUserController::class, 'index'])
        ->can('viewAny', User::class);
    Route::post('/users',        [AdminUserController::class, 'store'])
        ->can('create',  User::class);
    Route::get('/users/{user}',  [AdminUserController::class, 'show'])
        ->can('view',    'user');
    Route::put('/users/{user}',  [AdminUserController::class, 'update'])
        ->can('update',  'user');
    Route::delete('/users/{user}',[AdminUserController::class, 'destroy'])
        ->can('delete',  'user');
    Route::post('/users/{user}/resend-verification', [AdminUserController::class, 'resendVerification'])
        ->can('view', 'user');
});

// Example: restrict to permission
Route::middleware(['auth:sanctum', 'permission:view users'])->group(function () {
    // routes requiring a specific permission go here
});
