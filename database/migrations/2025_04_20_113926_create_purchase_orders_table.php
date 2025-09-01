<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->date('order_date');
            $table->string('supplier_id');
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('supplier_id')->references('supplier_id')->on('suppliers');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
