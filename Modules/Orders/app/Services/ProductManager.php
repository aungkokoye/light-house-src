<?php

namespace Modules\Orders\Services;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Orders\Models\PaymentPrice;
use Modules\Orders\Models\Product;

class ProductManager
{
    const array ALLOWED_SORTS = ['id', 'name', 'created_at', 'per_price'];

    public function list(Request $request, int $perPage): LengthAwarePaginator
    {
        $sortBy  = in_array($request->input('sort_by'), self::ALLOWED_SORTS) ? $request->input('sort_by') : 'name';
        $sortDir = $request->input('sort_dir') === 'desc' ? 'desc' : 'asc';

        $query = Product::with(['createdBy:id,name', 'prices' => fn($q) => $q->orderByDesc('updated_at')])
            ->addSelect([
                'products.*',
                'current_price' => DB::table('product_prices')
                    ->select('per_price')
                    ->whereColumn('product_id', 'products.id')
                    ->orderByDesc('updated_at')
                    ->limit(1),
            ]);

        if ($search = trim((string) $request->input('search'))) {
            $query->whereRaw('MATCH(name, description) AGAINST(? IN BOOLEAN MODE)', [$search . '*']);
        }

        if ($sortBy === 'per_price') {
            $query->orderBy('current_price', $sortDir);
        } else {
            $query->orderBy($sortBy, $sortDir);
        }

        return $query->paginate($perPage);
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
