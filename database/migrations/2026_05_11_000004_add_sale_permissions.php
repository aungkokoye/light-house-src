<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('permissions')->insert(
            collect( ['list','order_list', 'order_view', 'order_create', 'order_edit', 'order_delete'])
                ->map(fn ($name) => ['name' => $name, 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now])
                ->toArray()
        );

        $this->clearCache();
    }

    public function down(): void
    {
        DB::table('roles')
            ->where(
                'name',
                ['list','order_list', 'order_view', 'order_create', 'order_edit', 'order_delete']
            )->delete();

        $this->clearCache();
    }

    private function clearCache(): void
    {
        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));
    }
};
