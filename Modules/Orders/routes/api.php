<?php

use Illuminate\Support\Facades\Route;
use Modules\Orders\Http\Controllers\BankController;
use Modules\Orders\Http\Controllers\CustomerController;
use Modules\Orders\Http\Controllers\InvoiceController;
use Modules\Orders\Http\Controllers\JobServiceController;
use Modules\Orders\Http\Controllers\PaymentController;
use Modules\Orders\Http\Controllers\ProductController;
use Modules\Orders\Models\Bank;
use Modules\Orders\Models\Customer;
use Modules\Orders\Models\Invoice;
use Modules\Orders\Models\JobService;
use Modules\Orders\Models\Payment;
use Modules\Orders\Models\PaymentPrice;
use Modules\Orders\Models\Product;

Route::middleware(['auth:sanctum', 'role:admin|user'])->prefix('order')->group(function () {
    Route::get('/banks',           [BankController::class, 'index'])  ->can('viewAny', Bank::class);
    Route::post('/banks',          [BankController::class, 'store'])  ->can('create',  Bank::class);
    Route::get('/banks/{bank}',    [BankController::class, 'show'])   ->can('view',    'bank');
    Route::put('/banks/{bank}',    [BankController::class, 'update']) ->can('update',  'bank');
    Route::delete('/banks/{bank}', [BankController::class, 'destroy'])->can('delete',  'bank');

    Route::get('/services',                  [JobServiceController::class, 'index'])  ->can('viewAny', JobService::class);
    Route::post('/services',                 [JobServiceController::class, 'store'])  ->can('create',  JobService::class);
    Route::get('/services/{job_service}',    [JobServiceController::class, 'show'])   ->can('view',    'job_service');
    Route::put('/services/{job_service}',    [JobServiceController::class, 'update']) ->can('update',  'job_service');
    Route::delete('/services/{job_service}', [JobServiceController::class, 'destroy'])->can('delete',  'job_service');

    Route::get('/products',                             [ProductController::class, 'index'])       ->can('viewAny', Product::class);
    Route::post('/products',                            [ProductController::class, 'store'])       ->can('create',  Product::class);
    Route::get('/products/{product}',                   [ProductController::class, 'show'])        ->can('view',    'product');
    Route::put('/products/{product}',                   [ProductController::class, 'update'])      ->can('update',  'product');
    Route::delete('/products/{product}',                [ProductController::class, 'destroy'])     ->can('delete',  'product');
    Route::get('/products/{product}/prices',            [ProductController::class, 'prices'])       ->can('viewAny', PaymentPrice::class);
    Route::delete('/products/{product}/prices/{price}', [ProductController::class, 'destroyPrice']) ->can('delete',  'price');

    Route::get('/customers/search',          [CustomerController::class, 'search'])  ->can('viewAny', Customer::class);
    Route::get('/customers',                 [CustomerController::class, 'index'])   ->can('viewAny', Customer::class);
    Route::post('/customers',                [CustomerController::class, 'store'])   ->can('create',  Customer::class);
    Route::get('/customers/{customer}',      [CustomerController::class, 'show'])    ->can('view',    'customer');
    Route::put('/customers/{customer}',      [CustomerController::class, 'update'])  ->can('update',  'customer');
    Route::delete('/customers/{customer}',   [CustomerController::class, 'destroy']) ->can('delete',  'customer');

    Route::get('/invoices',                       [InvoiceController::class, 'index'])  ->can('viewAny', Invoice::class);
    Route::post('/invoices/{invoice}/send',        [InvoiceController::class, 'send'])  ->can('view', 'invoice');
    Route::post('/invoices',                       [InvoiceController::class, 'store']) ->can('create',  Invoice::class);
    Route::get('/invoices/{invoice}',              [InvoiceController::class, 'show'])  ->can('view',    'invoice');
    Route::put('/invoices/{invoice}',              [InvoiceController::class, 'update'])->can('update',  'invoice');
    Route::delete('/invoices/{invoice}',           [InvoiceController::class, 'destroy'])->can('delete', 'invoice');

    Route::get('/payments/meta',                   [PaymentController::class, 'meta']);
    Route::post('/payments',                       [PaymentController::class, 'store'])       ->can('create',  Payment::class);
    Route::get('/payments/{payment}',              [PaymentController::class, 'show'])        ->can('view',    'payment');
    Route::put('/payments/{payment}',              [PaymentController::class, 'update'])      ->can('update',  'payment');
    Route::delete('/payments/{payment}',           [PaymentController::class, 'destroy'])     ->can('delete',  'payment');
    Route::post('/payments/{payment}/send-receipt',[PaymentController::class, 'sendReceipt']) ->can('view',    'payment');
});
