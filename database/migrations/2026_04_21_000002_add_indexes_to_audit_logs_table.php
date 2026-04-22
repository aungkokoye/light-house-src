<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $indexes = collect(DB::select("SHOW INDEX FROM audit_logs"))->pluck('Key_name');

        Schema::table('audit_logs', function (Blueprint $table) use ($indexes) {
            if (! $indexes->contains('audit_logs_created_at_index')) {
                $table->index('created_at');
            }
            if (! $indexes->contains('audit_logs_user_email_index')) {
                $table->index('user_email');
            }
            if (! $indexes->contains('audit_logs_ip_address_index')) {
                $table->index('ip_address');
            }
        });
    }

    public function down(): void
    {
        $indexes = collect(DB::select("SHOW INDEX FROM audit_logs"))->pluck('Key_name');

        Schema::table('audit_logs', function (Blueprint $table) use ($indexes) {
            if ($indexes->contains('audit_logs_created_at_index')) {
                $table->dropIndex(['created_at']);
            }
            if ($indexes->contains('audit_logs_user_email_index')) {
                $table->dropIndex(['user_email']);
            }
            if ($indexes->contains('audit_logs_ip_address_index')) {
                $table->dropIndex(['ip_address']);
            }
        });
    }
};
