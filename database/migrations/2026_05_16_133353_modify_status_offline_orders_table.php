<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
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
        DB::statement("

            ALTER TABLE offline_orders

            MODIFY status ENUM(

                'menunggu',
                'disewakan',
                'pengembalian',
                'terlambat',
                'selesai',
                'dibatalkan'

            )

            DEFAULT 'menunggu'

        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        DB::statement("

            ALTER TABLE offline_orders

            MODIFY status ENUM(

                'menunggu'

            )

            DEFAULT 'menunggu'

        ");
    }
};
