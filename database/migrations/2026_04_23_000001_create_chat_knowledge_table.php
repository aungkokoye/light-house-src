<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_knowledge_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('sort_order');
        });

        Schema::create('chat_knowledge', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_knowledge_category_id')->constrained('chat_knowledge_categories')->cascadeOnDelete();
            $table->string('title');
            $table->text('content');
            $table->boolean('active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['active', 'sort_order']);
            $table->index('chat_knowledge_category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_knowledge');
        Schema::dropIfExists('chat_knowledge_categories');
    }
};
