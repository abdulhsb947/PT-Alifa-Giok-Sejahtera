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
        Schema::table('agreements', function (Blueprint $table) {
        $table->string('customer_file')->nullable(); // file dari customer (ttd)
        $table->text('admin_notes')->nullable();
        $table->text('customer_notes')->nullable();
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
