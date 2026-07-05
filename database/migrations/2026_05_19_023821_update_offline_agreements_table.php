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
        Schema::table('offline_agreements', function (Blueprint $table) {

    $table->string('requirement_type')
          ->nullable();

    $table->string('requirement_file')
          ->nullable();

    $table->decimal(
        'remaining_payment',
        15,
        2
    )->default(0);

    $table->string('final_payment_proof')
          ->nullable();
});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
