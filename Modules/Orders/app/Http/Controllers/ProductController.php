<?php

namespace Modules\Orders\Http\Controllers;

use App\Concerns\AuditableCrud;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Orders\Http\Requests\StoreProductRequest;
use Modules\Orders\Http\Requests\UpdateProductRequest;
use Modules\Orders\Models\PaymentPrice;
use Modules\Orders\Models\Product;
use Modules\Orders\Services\ProductManager;

class ProductController extends Controller
{
    use AuditableCrud;

    const int DEFAULT_PER_PAGE = 15;
    const array PER_PAGE_LIST = [5, 10, 15, 25, 50];

    public function __construct(private readonly ProductManager $productManager) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = in_array((int) $request->input('per_page'), self::PER_PAGE_LIST)
            ? (int) $request->input('per_page')
            : self::DEFAULT_PER_PAGE;

        return response()->json($this->productManager->list($request, $perPage));
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productManager->create($request->validated());
        $this->auditCreated($product, $this->snapshot($product));

        return response()->json($product, 201);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json($this->productManager->show($product));
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product->load(['createdBy:id,name', 'prices']);
        $oldValues = $this->snapshot($product);

        $updated = $this->productManager->update($product, $request->validated());
        $this->auditUpdated($updated, $oldValues, $this->snapshot($updated));

        return response()->json($updated);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->load(['createdBy:id,name', 'prices']);
        $this->auditDeleted($product, $this->snapshot($product));
        $this->productManager->delete($product);

        return response()->json(['message' => 'Product deleted successfully.']);
    }

    public function prices(Product $product): JsonResponse
    {
        return response()->json(
            $product->prices()->orderByDesc('updated_at')->get()
        );
    }

    public function destroyPrice(Product $product, PaymentPrice $price): JsonResponse
    {
        if ($product->prices()->count() <= 1) {
            return response()->json(['message' => 'Cannot delete the last price.'], 422);
        }

        $price->delete();

        return response()->json(['message' => 'Price deleted.']);
    }

    private function snapshot(Product $product): array
    {
        $attrs = $this->filterAuditValues($product->getAttributes());

        unset($attrs['created_by']);

        return array_merge($attrs, [
            'created_by' => ['id' => $product->createdBy?->id, 'name' => $product->createdBy?->name],
            'prices'     => $product->prices->map(fn($p) => ['id' => $p->id, 'per_price' => $p->per_price])->toArray(),
        ]);
    }
}
