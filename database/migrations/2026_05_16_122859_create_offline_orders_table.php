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
        Schema::create('offline_orders', function (Blueprint $table) {
            $table->id();
             $table->string('order_code');

            $table->string('customer_name');
            $table->string('phone')->nullable();

            $table->string('project_name');
            $table->text('project_location');

            $table->date('start_date');

            $table->integer('duration');
            $table->string('duration_unit')->default('bulan');

            $table->text('notes')->nullable();

            $table->decimal('total_price', 15,2)->default(0);

            $table->foreignId('created_by');

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
        Schema::dropIfExists('offline_orders');
    }
};
