<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('agreements', function (Blueprint $table) {
            if (!Schema::hasColumn('agreements', 'signature_file')) {
                $table->string('signature_file')->nullable();
            }

            if (!Schema::hasColumn('agreements', 'final_file')) {
                $table->string('final_file')->nullable();
            }

            if (!Schema::hasColumn('agreements', 'version')) {
                $table->integer('version')->default(1);
            }
        });
    }

    public function down()
    {
        $columns = array_values(array_filter([
            Schema::hasColumn('agreements', 'signature_file') ? 'signature_file' : null,
            Schema::hasColumn('agreements', 'final_file') ? 'final_file' : null,
            Schema::hasColumn('agreements', 'version') ? 'version' : null,
        ]));

        if ($columns) {
            Schema::table('agreements', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
