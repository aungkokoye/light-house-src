<?php

namespace Modules\Orders\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Concerns\HasAbilities;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Orders\Http\Requests\StoreBankRequest;
use Modules\Orders\Http\Requests\UpdateBankRequest;
use Modules\Orders\Models\Bank;
use Modules\Orders\Services\BankManager;

class BankController extends Controller
{
    use HasAbilities;

    public function __construct(private readonly BankManager $bankManager) {}

    public function index(Request $request): JsonResponse
    {
        $perPage  = (int) $request->input('per_page', 20);
        $paginated = $this->bankManager->list($request, $perPage);

        return response()->json(array_merge($paginated->toArray(), [
            'can' => $this->listAbilities(Bank::class),
        ]));
    }

    public function store(StoreBankRequest $request): JsonResponse
    {
        return response()->json($this->bankManager->create($request->validated()), 201);
    }

    public function show(Bank $bank): JsonResponse
    {
        return response()->json(array_merge($this->bankManager->show($bank)->toArray(), [
            'can' => $this->resourceAbilities($bank),
        ]));
    }

    public function update(UpdateBankRequest $request, Bank $bank): JsonResponse
    {
        return response()->json($this->bankManager->update($bank, $request->validated()));
    }

    public function destroy(Bank $bank): JsonResponse
    {
        try {
            $this->bankManager->delete($bank);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json(['message' => 'Cannot delete this bank because it is used in existing payments.'], 422);
            }
            throw $e;
        }

        return response()->json(['message' => 'Bank deleted successfully.']);
    }
}
