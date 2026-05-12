<?php

namespace Modules\Orders\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Orders\Http\Requests\StoreBankRequest;
use Modules\Orders\Http\Requests\UpdateBankRequest;
use Modules\Orders\Models\Bank;
use Modules\Orders\Services\BankManager;

class BankController extends Controller
{
    public function __construct(private readonly BankManager $bankManager) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 20);

        return response()->json($this->bankManager->list($request, $perPage));
    }

    public function store(StoreBankRequest $request): JsonResponse
    {
        return response()->json($this->bankManager->create($request->validated()), 201);
    }

    public function show(Bank $bank): JsonResponse
    {
        return response()->json($this->bankManager->show($bank));
    }

    public function update(UpdateBankRequest $request, Bank $bank): JsonResponse
    {
        return response()->json($this->bankManager->update($bank, $request->validated()));
    }

    public function destroy(Bank $bank): JsonResponse
    {
        $this->bankManager->delete($bank);

        return response()->json(['message' => 'Bank deleted successfully.']);
    }
}
