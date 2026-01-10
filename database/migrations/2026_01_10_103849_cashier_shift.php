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
        Schema::create('cashier_shifts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cashier_id');
            $table->timestamp('shift_start');
            $table->timestamp('shift_end')->nullable();
            $table->decimal('starting_cash', 10, 2);
            $table->decimal('ending_cash', 10, 2)->nullable();
            $table->decimal('return_cash', 10, 2)->default(0);
            $table->string('status')->default('open');
            $table->timestamps();
            $table->foreign('cashier_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashier_shifts');
    }
};
