<?php

namespace Modules\Orders\Http\Controllers;

use App\Concerns\AuditableCrud;
use App\Concerns\HasAbilities;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Orders\Http\Requests\StorePriceListRequest;
use Modules\Orders\Http\Requests\UpdatePriceListRequest;
use Modules\Orders\Models\PriceList;
use Modules\Orders\Services\PriceListManager;

class PriceListController extends Controller
{
    use AuditableCrud, HasAbilities;

    const int DEFAULT_PER_PAGE = 15;
    const array PER_PAGE_LIST = [5, 10, 15, 25, 50];

    public function __construct(private readonly PriceListManager $manager) {}

    public function index(Request $request): JsonResponse
    {
        $perPage   = in_array((int) $request->input('per_page'), self::PER_PAGE_LIST)
            ? (int) $request->input('per_page')
            : self::DEFAULT_PER_PAGE;
        $paginated = $this->manager->list($request, $perPage);

        return response()->json(array_merge($paginated->toArray(), [
            'can' => $this->listAbilities(PriceList::class),
        ]));
    }

    public function store(StorePriceListRequest $request): JsonResponse
    {
        $priceList = $this->manager->create($request->validated());
        $this->auditCreated($priceList, $this->snapshot($priceList));

        return response()->json($priceList, 201);
    }

    public function show(PriceList $priceList): JsonResponse
    {
        return response()->json(array_merge($this->manager->show($priceList)->toArray(), [
            'can' => $this->resourceAbilities($priceList),
        ]));
    }

    public function update(UpdatePriceListRequest $request, PriceList $priceList): JsonResponse
    {
        $oldValues = $this->snapshot($priceList);
        $updated   = $this->manager->update($priceList, $request->validated());
        $this->auditUpdated($updated, $oldValues, $this->snapshot($updated));

        return response()->json($updated);
    }

    public function destroy(PriceList $priceList): JsonResponse
    {
        $this->auditDeleted($priceList, $this->snapshot($priceList));
        $this->manager->delete($priceList);

        return response()->json(['message' => 'Price list entry deleted successfully.']);
    }

    private function snapshot(PriceList $priceList): array
    {
        $attrs = $this->filterAuditValues($priceList->getAttributes());

        unset($attrs['job_service_id'], $attrs['created_by']);

        return array_merge($attrs, [
            'job_service' => ['id' => $priceList->jobService?->id, 'name' => $priceList->jobService?->name],
            'created_by'  => ['id' => $priceList->createdBy?->id, 'name' => $priceList->createdBy?->name],
        ]);
    }
}
