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
        Schema::create('return_items', function (Blueprint $table) {
    $table->id();

    $table->foreignId('rental_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->foreignId('product_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->integer('damaged_qty')->default(0);
    $table->integer('lost_qty')->default(0);

    $table->decimal('repair_cost', 15, 2)->default(0);
    $table->decimal('lost_cost', 15, 2)->default(0);

    $table->text('notes')->nullable();

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
        Schema::dropIfExists('return_items');
    }
};
