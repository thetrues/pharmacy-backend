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
         /*{"cashAmount":2000,"cardAmount":0,"creditAmount":0,"change":704,"total":1296,"sale_id":1,"order_number":"ORD-20260214-0001","payment_method":"cash"}*/
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->integer('sales_id');
            $table->integer('cashier_id');
            $table->integer('shift_id');
            $table->string('order_number')->unique();
            $table->decimal('cash_amount', 10, 2);
            $table->decimal('card_amount', 10, 2);
            $table->decimal('credit_amount', 10, 2);
            $table->decimal('change', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('amount_paid', 10, 2);
            $table->date('payment_date');
            $table->enum('payment_method', ['cash', 'credit', 'card', 'mobile_payment']);
            $table->text('remarks')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed'])->default('completed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
