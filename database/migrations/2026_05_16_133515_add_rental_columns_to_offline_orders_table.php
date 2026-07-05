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
            $table->date('return_date')
                  ->nullable();

            $table->integer('late_days')
                  ->default(0);

            $table->decimal('penalty', 15,2)
                  ->default(0);

        
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
            $table->dropColumn([
                'return_date',
                'late_days',
                'penalty'
            ]);
        });
    }
};
