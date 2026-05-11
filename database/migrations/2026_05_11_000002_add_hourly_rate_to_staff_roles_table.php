<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_roles', function (Blueprint $table) {
            $table->unsignedInteger('overtime_hourly_rate')->nullable()->after('salary');
        });
    }

    public function down(): void
    {
        Schema::table('staff_roles', function (Blueprint $table) {
            $table->dropColumn('overtime_hourly_rate');
        });
    }
};
