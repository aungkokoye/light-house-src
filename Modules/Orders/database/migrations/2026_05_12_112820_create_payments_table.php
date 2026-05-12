<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->unsignedTinyInteger('type_id');             // 1:cash 2:bank 3:other
            $table->foreignId('bank_id')->nullable()->constrained('banks'); // required when type_id = 2
            $table->unsignedTinyInteger('stage');               // 1:advance/deposit 2:final
            $table->unsignedBigInteger('amount');
            $table->text('note')->nullable();
            $table->date('payment_date');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['invoice_id', 'stage']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
