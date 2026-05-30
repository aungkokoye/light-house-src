<?php

namespace Modules\Orders\Http\Controllers;

use App\Concerns\HasAbilities;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Orders\Http\Requests\StoreCustomerRequest;
use Modules\Orders\Http\Requests\UpdateCustomerRequest;
use Modules\Orders\Models\Customer;
use Modules\Orders\Services\CustomerManager;

class CustomerController extends Controller
{
    use HasAbilities;

    public function __construct(private readonly CustomerManager $manager) {}

    public function search(Request $request): JsonResponse
    {
        return response()->json($this->manager->search($request->input('search')));
    }

    public function index(Request $request): JsonResponse
    {
        $perPage   = (int) $request->input('per_page', 20);
        $paginated = $this->manager->list($request, $perPage);

        return response()->json(array_merge($paginated->toArray(), [
            'can' => $this->listAbilities(Customer::class),
        ]));
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        return response()->json($this->manager->create($request->validated()), 201);
    }

    public function show(Customer $customer): JsonResponse
    {
        return response()->json(array_merge($this->manager->show($customer)->toArray(), [
            'can' => $this->resourceAbilities($customer),
        ]));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): JsonResponse
    {
        return response()->json($this->manager->update($customer, $request->validated()));
    }

    public function destroy(Customer $customer): JsonResponse
    {
        try {
            $this->manager->delete($customer);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json(['message' => 'Cannot delete this customer because they have existing invoices.'], 422);
            }
            throw $e;
        }

        return response()->json(['message' => 'Customer deleted successfully.']);
    }
}
