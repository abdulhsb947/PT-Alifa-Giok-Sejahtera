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
    public function up(): void
    {
        Schema::create('offline_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offline_order_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('product_id')
                ->constrained();

            $table->integer('quantity');

            $table->decimal('price', 15,2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('offline_order_items');
    }
};
