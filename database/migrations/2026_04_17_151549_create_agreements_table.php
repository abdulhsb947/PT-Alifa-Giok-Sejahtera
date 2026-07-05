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
        Schema::create('agreements', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('order_id');
        $table->string('file');
        $table->timestamp('admin_signed_at')->nullable();
        $table->timestamp('customer_signed_at')->nullable();
        $table->string('status')->default('pending');
        $table->timestamps();

        $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agreements');
    }
};
