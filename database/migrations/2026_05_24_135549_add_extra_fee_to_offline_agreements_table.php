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

            $table->bigInteger('tax')->default(0);

            $table->bigInteger('admin_fee')->default(0);

            $table->bigInteger('shipping_fee')->default(0);

            $table->bigInteger('other_fee')->default(0);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offline_agreements', function (Blueprint $table) {

            $table->dropColumn([
                'tax',
                'admin_fee',
                'shipping_fee',
                'other_fee'
            ]);

        });
    }
};
