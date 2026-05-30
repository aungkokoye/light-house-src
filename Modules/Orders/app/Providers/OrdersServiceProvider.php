<?php

namespace Modules\Orders\Providers;

use Illuminate\Support\Facades\Gate;
use Modules\Orders\Models\Customer;
use Modules\Orders\Models\Invoice;
use Modules\Orders\Models\JobService;
use Modules\Orders\Models\Payment;
use Modules\Orders\Models\PaymentPrice;
use Modules\Orders\Models\Product;
use Modules\Orders\Models\PaymentType;
use Modules\Orders\Policies\CustomerPolicy;
use Modules\Orders\Policies\PaymentTypePolicy;
use Modules\Orders\Policies\InvoicePolicy;
use Modules\Orders\Policies\JobServicePolicy;
use Modules\Orders\Policies\PaymentPolicy;
use Modules\Orders\Policies\ProductPolicy;
use Modules\Orders\Policies\ProductPricePolicy;
use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class OrdersServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Orders';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'orders';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    // protected array $commands = [];

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function boot(): void
    {
        parent::boot();

        Gate::policy(PaymentType::class,   PaymentTypePolicy::class);
        Gate::policy(Customer::class,     CustomerPolicy::class);
        Gate::policy(Invoice::class,      InvoicePolicy::class);
        Gate::policy(JobService::class,   JobServicePolicy::class);
        Gate::policy(Payment::class,      PaymentPolicy::class);
        Gate::policy(Product::class,      ProductPolicy::class);
        Gate::policy(PaymentPrice::class, ProductPricePolicy::class);
    }

    /**
     * Define module schedules.
     * 
     * @param $schedule
     */
    // protected function configureSchedules(Schedule $schedule): void
    // {
    //     $schedule->command('inspire')->hourly();
    // }
}
