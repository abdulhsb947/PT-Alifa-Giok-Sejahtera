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
        Schema::create('offline_agreements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('offline_order_id')
                  ->constrained()
                  ->onDelete('cascade');

            // FILE
            $table->enum('requirement_type', [

    'ktp',
    'npwp',
    'spk'

])->nullable();

$table->string('requirement_file')
      ->nullable();

$table->string('agreement_file')
      ->nullable();

            // PAYMENT
            $table->enum('payment_type', [

                'dp',
                'lunas'

            ])->default('dp');

            $table->decimal('payment_amount', 15,2)
                  ->default(0);

            $table->string('payment_proof')
                  ->nullable();

            // NOTES
            $table->text('notes')
                  ->nullable();

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
        Schema::dropIfExists('offline_agreements');
    }
};
