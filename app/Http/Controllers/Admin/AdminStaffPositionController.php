<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffPosition;
use Illuminate\Http\JsonResponse;

class AdminStaffPositionController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(StaffPosition::orderBy('name')->get(['id', 'name']));
    }
}