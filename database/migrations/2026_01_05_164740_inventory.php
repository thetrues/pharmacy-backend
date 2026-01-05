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
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('SKU')->unique();
            $table->string('supplier')->nullable();
            $table->string('batch_number')->unique();
            $table->date('expiry_date');
            $table->integer('quantity');
            $table->integer('reorder_level')->default(0);
            $table->integer('stock')->default(0);
            $table->float('cost_price');
            $table->float('selling_price');
            $table->boolean('is_active')->default(true);
            $table->foreignId('added_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
