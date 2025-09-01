<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('sale_number')->unique(); // Contoh: PJ20250511-001
            $table->date('sale_date');
            $table->string('buyer_name')->nullable();
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total_payment', 15, 2); // Total setelah dikurangi diskon
            $table->enum('payment_method', ['Tunai', 'Transfer']);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
};
