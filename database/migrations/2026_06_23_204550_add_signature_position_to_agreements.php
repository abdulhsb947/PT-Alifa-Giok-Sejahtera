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

            $table->integer('signature_page')->default(1);

            $table->float('signature_x')->nullable();

            $table->float('signature_y')->nullable();

            $table->float('signature_width')->default(40);

            $table->float('signature_height')->default(20);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agreements', function (Blueprint $table) {

            $table->dropColumn([

                'signature_page',
                'signature_x',
                'signature_y',
                'signature_width',
                'signature_height'

            ]);
        });
    }
};
