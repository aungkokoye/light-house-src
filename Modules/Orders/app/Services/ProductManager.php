<?php

namespace Modules\Orders\Services;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Orders\Filters\ProductFilter;
use Modules\Orders\Models\PaymentPrice;
use Modules\Orders\Models\Product;

class ProductManager
{
    public function list(Request $request, int $perPage): LengthAwarePaginator
    {
        $query = Product::withCount('invoiceJobs')->with(['createdBy:id,name', 'prices' => fn($q) => $q->orderByDesc('updated_at')])
            ->addSelect([
                'products.*',
                'current_price' => DB::table('product_prices')
                    ->select('per_price')
                    ->whereColumn('product_id', 'products.id')
                    ->orderByDesc('updated_at')
                    ->limit(1),
            ]);

        return ProductFilter::for($query)
            ->search($request->input('search'))
            ->sort($request->input('sort_by', 'name'), $request->input('sort_dir', 'asc'))
            ->query()
            ->paginate($perPage);
    }

    public function show(Product $product): Product
    {
        return $product->load(['createdBy:id,name', 'prices' => fn($q) => $q->orderByDesc('updated_at')]);
    }

    public function create(array $data): Product
    {
        $perPrice = $data['per_price'];
        unset($data['per_price']);

        $product = Product::create([...$data, 'created_by' => Auth::id()]);

        $product->prices()->create([
            'per_price'  => $perPrice,
            'created_by' => Auth::id(),
        ]);

        return $product->load(['createdBy:id,name', 'prices' => fn($q) => $q->orderByDesc('updated_at')]);
    }

    public function update(Product $product, array $data): Product
    {
        $perPrice = $data['per_price'] ?? null;
        unset($data['per_price']);

        $product->update($data);

        if ($perPrice !== null) {
            $product->prices()->create([
                'per_price'  => $perPrice,
                'created_by' => Auth::id(),
            ]);
        }

        return $product->refresh()->load(['createdBy:id,name', 'prices' => fn($q) => $q->orderByDesc('updated_at')]);
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }
}
