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
        Schema::create('orders', function (Blueprint $table) {
            $table->string('order_code');
            $table->string('customer_name');
            $table->string('project_name');
            $table->string('project_location');
            $table->string('product_name');
            $table->integer('quantity');
            $table->string('status');
            $table->date('start_date');
            $table->integer('duration');
            $table->string('duration_unit');
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
        Schema::dropIfExists('orders');
    }
};
