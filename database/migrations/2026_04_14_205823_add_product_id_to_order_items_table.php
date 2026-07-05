<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductIdToOrderItemsTable extends Migration
{
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
           $table->unsignedBigInteger('product_id'); // ✅ WAJIB
        });
    }

    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('product_id');
        });
    }
}
