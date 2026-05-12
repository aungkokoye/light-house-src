<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index('name');
            $table->index('created_at');
            $table->fullText(['name', 'description'], 'products_fulltext');
        });

        Schema::table('product_prices', function (Blueprint $table) {
            $table->index('updated_at');
        });

        Schema::table('job_services', function (Blueprint $table) {
            $table->index('name');
            $table->index('created_at');
            $table->fullText(['name', 'description'], 'job_services_fulltext');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['created_at']);
            $table->dropFullText('products_fulltext');
        });

        Schema::table('product_prices', function (Blueprint $table) {
            $table->dropIndex(['updated_at']);
        });

        Schema::table('job_services', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['created_at']);
            $table->dropFullText('job_services_fulltext');
        });
    }
};
