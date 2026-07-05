<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            ALTER TABLE offline_orders
            MODIFY status ENUM(
                'menunggu',
                'disewakan',
                'selesai'
            ) DEFAULT 'menunggu'
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("
            ALTER TABLE offline_orders
            MODIFY status ENUM(
                'menunggu'
            ) DEFAULT 'menunggu'
        ");
    }
};
