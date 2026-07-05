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
        Schema::create('payments', function (Blueprint $table) {
        $table->id();

        $table->foreignId('order_id')
              ->constrained()
              ->onDelete('cascade');

        $table->string('proof');
        $table->decimal('amount', 12, 2);

        $table->enum('payment_type', ['dp', 'lunas']);
        $table->enum('status', [
            'waiting_verification',
            'approved',
            'rejected'
        ])->default('waiting_verification');

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
        Schema::dropIfExists('payments');
    }
};
