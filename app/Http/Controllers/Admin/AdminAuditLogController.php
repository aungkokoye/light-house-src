<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Services\AuditLogManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminAuditLogController extends Controller
{
    public function __construct(private readonly AuditLogManager $manager) {}

    public function events(): JsonResponse
    {
        return response()->json(AuditLog::events());
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 20);

        return response()->json($this->manager->list($request, $perPage));
    }

    public function show(AuditLog $auditLog): JsonResponse
    {
        return response()->json($this->manager->show($auditLog));
    }
}