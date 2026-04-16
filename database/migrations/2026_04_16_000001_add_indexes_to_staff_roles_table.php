<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_roles', function (Blueprint $table) {
            // Heavily used in whereNull('end_date') and ORDER BY (end_date IS NULL) DESC
            $table->index('end_date');

            // Composite: all active-role lookups filter by staff_profile_id + end_date
            $table->index(['staff_profile_id', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::table('staff_roles', function (Blueprint $table) {
            $table->dropIndex(['end_date']);
            $table->dropIndex(['staff_profile_id', 'end_date']);
        });
    }
};
