<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offline_order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('offline_order_items', 'returned_qty')) {
                $table->integer('returned_qty')->nullable()->after('price');
            }

            if (! Schema::hasColumn('offline_order_items', 'damaged_qty')) {
                $table->integer('damaged_qty')->default(0)->after('returned_qty');
            }

            if (! Schema::hasColumn('offline_order_items', 'lost_qty')) {
                $table->integer('lost_qty')->default(0)->after('damaged_qty');
            }

            if (! Schema::hasColumn('offline_order_items', 'repair_cost')) {
                $table->decimal('repair_cost', 15, 2)->default(0)->after('lost_qty');
            }

            if (! Schema::hasColumn('offline_order_items', 'lost_cost')) {
                $table->decimal('lost_cost', 15, 2)->default(0)->after('repair_cost');
            }

            if (! Schema::hasColumn('offline_order_items', 'return_notes')) {
                $table->text('return_notes')->nullable()->after('lost_cost');
            }
        });
    }

    public function down(): void
    {
        Schema::table('offline_order_items', function (Blueprint $table) {
            $columns = [
                'return_notes',
                'lost_cost',
                'repair_cost',
                'lost_qty',
                'damaged_qty',
                'returned_qty',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('offline_order_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
