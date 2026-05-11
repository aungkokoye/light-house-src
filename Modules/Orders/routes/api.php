<?php

use Illuminate\Support\Facades\Route;
use Modules\Orders\Http\Controllers\BankController;
use Modules\Orders\Models\Bank;

Route::middleware(['auth:sanctum', 'role:admin|sale'])->prefix('order')->group(function () {
    Route::get('/banks',           [BankController::class, 'index'])  ->can('viewAny', Bank::class);
    Route::post('/banks',          [BankController::class, 'store'])  ->can('create',  Bank::class);
    Route::get('/banks/{bank}',    [BankController::class, 'show'])   ->can('view',    'bank');
    Route::put('/banks/{bank}',    [BankController::class, 'update']) ->can('update',  'bank');
    Route::delete('/banks/{bank}', [BankController::class, 'destroy'])->can('delete',  'bank');
});
