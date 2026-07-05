<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        $payments = DB::table('payments')
            ->join('orders', 'payments.order_id', '=', 'orders.id')
            ->where('payments.payment_type', 'pelunasan')
            ->whereNotNull('orders.start_date')
            ->select(
                'payments.id',
                'orders.start_date',
                'orders.duration',
                'orders.duration_unit'
            )
            ->get();

        foreach ($payments as $payment) {
            $start = Carbon::parse($payment->start_date);
            $duration = max(1, (int) $payment->duration);

            if ($payment->duration_unit == 'hari') {
                $endDate = $start->copy()->addDays($duration);
            } elseif ($payment->duration_unit == 'minggu') {
                $endDate = $start->copy()->addWeeks($duration);
            } else {
                $endDate = $start->copy()->addMonths($duration);
            }

            $dueDate = $endDate->copy()->subDays(7);

            if ($dueDate->lt($start)) {
                $dueDate = $start;
            }

            DB::table('payments')
                ->where('id', $payment->id)
                ->update([
                    'due_date' => $dueDate->toDateString(),
                ]);
        }
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
