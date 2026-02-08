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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('orderNumber')->unique();
            $table->unsignedBigInteger('customer_id')->nullable();
             $table->decimal('subtotalAmount', 10, 2);
            $table->decimal('taxAmount', 10, 2);
            $table->decimal('totalAmount', 10, 2);
            $table->integer('customerId')->nullable();
            $table->string('customerName')->nullable();
            $table->string('status')->default('pending');
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
