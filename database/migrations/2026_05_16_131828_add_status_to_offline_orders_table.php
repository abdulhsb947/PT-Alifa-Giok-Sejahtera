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
        Schema::table('offline_orders', function (Blueprint $table) {
             $table->enum('status', [

                'menunggu',
                'disewakan',
                'selesai'

            ])->default('menunggu');

        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('offline_orders', function (Blueprint $table) {
             $table->dropColumn('status');
        });
    }
};
