<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'total_tagihan')) {
                $table->decimal('total_tagihan', 12, 2)->default(0)->after('amount');
            }

            if (!Schema::hasColumn('payments', 'sisa_pembayaran')) {
                $table->decimal('sisa_pembayaran', 12, 2)->default(0)->after('total_tagihan');
            }

            if (!Schema::hasColumn('payments', 'due_date')) {
                $table->date('due_date')->nullable()->after('notes');
            }
        });

        DB::statement("ALTER TABLE payments MODIFY proof VARCHAR(255) NULL");
        DB::statement("ALTER TABLE payments MODIFY payment_type ENUM('tagihan','dp','lunas','pelunasan','penalty') NOT NULL");
        DB::statement("ALTER TABLE payments MODIFY status ENUM('menunggu_pembayaran','menunggu_verifikasi','disetujui','ditolak') NOT NULL DEFAULT 'menunggu_verifikasi'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE payments MODIFY payment_type ENUM('dp','lunas') NOT NULL");
        DB::statement("ALTER TABLE payments MODIFY status ENUM('waiting_verification','approved','rejected') NOT NULL DEFAULT 'waiting_verification'");
    }
};
