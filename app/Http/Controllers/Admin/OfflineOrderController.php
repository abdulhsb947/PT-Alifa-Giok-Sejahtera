<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Maintenance;
use App\Models\OfflineOrder;
use App\Models\OfflineOrderItem;
use App\Models\OfflineAgreement;

use Illuminate\Support\Facades\DB;

class OfflineOrderController extends Controller
{
    // ==========================
    // LIST
    // ==========================
    public function index()
    {
        $orders = OfflineOrder::with('items')
            ->latest()
            ->get();

        return view(
            'admin.offline-orders.index',
            compact('orders')
        );
    }

    // ==========================
    // FORM CREATE
    // ==========================
    public function create()
    {
        $products = Product::all();

        return view(
            'admin.offline-orders.create',
            compact('products')
        );
    }

    // ==========================
    // SIMPAN PEMESANAN
    // ==========================
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $order = OfflineOrder::create([

                'order_code' =>
                    'OFF-' . rand(1000,9999),

                'customer_name' =>
                    $request->customer_name,

                'phone' =>
                    $request->phone,

                'project_name' =>
                    $request->project_name,

                'project_location' =>
                    $request->project_location,

                'start_date' =>
                    $request->start_date,

                'duration' =>
                    $request->duration,

                'duration_unit' =>
                    'bulan',

                'notes' =>
                    $request->notes,

                // STATUS AWAL
                'status' => 'menunggu',

                'created_by' =>
                    auth()->id()
            ]);

            $total = 0;

            foreach ($request->products as $productId)
            {
                $product =
                    Product::findOrFail($productId);

                $qty =
                    (int) $request
                    ->quantities[$productId];

                // VALIDASI
                if ($qty <= 0)
                {
                    throw new \Exception(
                        'Jumlah tidak valid'
                    );
                }

                // CEK STOK
                if (
                    $qty >
                    $product->available_stock
                ) {
                    throw new \Exception(
                        'Stok tidak cukup untuk '
                        . $product->name
                    );
                }

                // SIMPAN ITEM
                OfflineOrderItem::create([

                    'offline_order_id' =>
                        $order->id,

                    'product_id' =>
                        $productId,

                    'quantity' =>
                        $qty,

                    'price' =>
                        $product->price_per_month
                ]);

                // HITUNG TOTAL
                $total +=
                    $qty *
                    $product->price_per_month;
            }

            // UPDATE TOTAL
            $order->update([
                'total_price' => $total
            ]);

            DB::commit();

            return redirect()
                ->route(
                    'offline-orders.index'
                )
                ->with(
                    'success',
                    'Pemesanan berhasil dibuat'
                );

        } catch (\Exception $e) {

            DB::rollback();

            return back()->with(
                'error',
                $e->getMessage()
            );
        }
    }

    // ==========================
    // AKTIFKAN PENYEWAAN
    // ==========================
    public function activateRental($id)
{
    DB::beginTransaction();

    try {

        $order = OfflineOrder::whereKey($id)
            ->lockForUpdate()
            ->firstOrFail();

        $order->load([
            'items.product',
            'agreement'
        ]);

        // ======================
        // CEK STATUS
        // ======================

        if (
            $order->status !=
            'siap_disewakan'
        ) {
            throw new \Exception(
                'Dokumen belum lengkap'
            );
        }

        // ======================
        // CEK STOK
        // ======================

        foreach ($order->items as $item)
        {
            $product = Product::whereKey($item->product_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($item->quantity > $product->available_stock) {
                throw new \Exception(
                    'Stok tidak cukup'
                );
            }
        }

        // ======================
        // KURANGI STOK
        // ======================

        foreach ($order->items as $item)
        {
            $product = Product::whereKey($item->product_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($item->quantity > $product->available_stock) {
                throw new \Exception(
                    'Stok tidak cukup'
                );
            }

            $product->decrement(
                'available_stock',
                $item->quantity
            );

            $product->increment(
                'rented_stock',
                $item->quantity
            );
        }

        // ======================
        // UPDATE STATUS
        // ======================

        $order->update([
            'status' => 'disewakan'
        ]);

        DB::commit();

        return back()->with(
            'success',
            'Penyewaan berhasil diaktifkan'
        );

    } catch (\Exception $e) {

        DB::rollback();

        return back()->with(
            'error',
            $e->getMessage()
        );
    }
}

    // ==========================
    // SELESAIKAN PENYEWAAN
    // ==========================
    

    public function processReturn(
    Request $request,
    $id
)
{
    DB::beginTransaction();

    try {

        $order = OfflineOrder::whereKey($id)
            ->lockForUpdate()
            ->firstOrFail();

        $order->load([
            'items.product',
            'agreement'
        ]);

        $request->validate([
            'late_days' => 'nullable|integer|min:0',
            'penalty' => 'nullable|numeric|min:0',
            'returned_qty' => 'required|array',
            'returned_qty.*' => 'integer|min:0',
            'damaged_qty' => 'nullable|array',
            'damaged_qty.*' => 'integer|min:0',
            'lost_qty' => 'nullable|array',
            'lost_qty.*' => 'integer|min:0',
            'repair_cost' => 'nullable|array',
            'repair_cost.*' => 'numeric|min:0',
            'lost_cost' => 'nullable|array',
            'lost_cost.*' => 'numeric|min:0',
            'return_notes' => 'nullable|array',
            'return_notes.*' => 'nullable|string',
        ]);

        // ======================
        // VALIDASI STATUS
        // ======================

        if (

    $order->status != 'pengembalian'

    &&

    $order->status != 'terlambat'

)
{
    throw new \Exception(
        'Pengembalian belum dimulai'
    );
}

        if (!$request->has('returned_qty'))
        {
            throw new \Exception(
                'Data pengecekan barang belum diisi'
            );
        }

        // ======================
        // PROCESS ITEM
        // ======================

        $totalRepairCost = 0;
        $totalLostCost = 0;

        foreach ($order->items as $item)
{
    $returnedQty =
        (int) $request->input(
            'returned_qty.' . $item->id,
            0
        );

    $damagedQty =
        (int) $request->input(
            'damaged_qty.' . $item->id,
            0
        );

    $lostQty =
        (int) $request->input(
            'lost_qty.' . $item->id,
            0
        );

    $repairCost =
        (int) $request->input(
            'repair_cost.' . $item->id,
            0
        );

    $lostCost =
        (int) $request->input(
            'lost_cost.' . $item->id,
            0
        );

    // VALIDASI
    if (
        $returnedQty < 0
        ||
        $damagedQty < 0
        ||
        $lostQty < 0
        ||
        $repairCost < 0
        ||
        $lostCost < 0
    ) {
        throw new \Exception(
            'Jumlah pengembalian tidak boleh minus'
        );
    }

    if (
        $damagedQty
        >
        $returnedQty
    ) {
        throw new \Exception(
            'Barang rusak tidak boleh melebihi barang kembali'
        );
    }

    if (
        ($returnedQty + $lostQty)
        !=
        $item->quantity
    ) {
        throw new \Exception(
            'Jumlah barang kembali dan hilang harus sesuai qty sewa'
        );
    }

    $item->update([

        'returned_qty' =>
            $returnedQty,

        'damaged_qty' =>
            $damagedQty,

        'lost_qty' =>
            $lostQty,

        'repair_cost' =>
            $repairCost,

        'lost_cost' =>
            $lostCost,

        'return_notes' =>
            $request->input(
                'return_notes.' . $item->id
            )
    ]);

    $totalRepairCost += $repairCost;
    $totalLostCost += $lostCost;

        }

        // ======================
// CEK TOTAL KERUSAKAN
// ======================



$penalty =
    (int) $request->input('penalty', 0);

$lateDays =
    (int) $request->input('late_days', 0);

$totalReturnCharge =
    $penalty
    + $totalRepairCost
    + $totalLostCost;

$nextStatus =
    $order->agreement
    &&
    (int) ($order->agreement?->remaining_payment ?? 0) <= 0
    &&
    (int) $totalReturnCharge <= 0
        ? 'selesai'
        : 'pengembalian';

$order->load([
    'items.product',
    'agreement'
]);

if ($nextStatus == 'selesai' && $order->status != 'selesai')
{
    $this->finalizeReturnStock($order);
}

$order->update([

    'status' => $nextStatus,

    'penalty' =>
        $penalty,

    'late_days' =>
        $lateDays

]);

        $order->refresh();

$order->load([
    'items.product',
    'agreement'
]);


        $order->save();

DB::commit();

$order = OfflineOrder::with([
    'items.product',
    'agreement'
])->findOrFail($id);

return back()->with(
    'success',
    'Pengembalian berhasil diproses'
);

    } catch (\Exception $e) {

        DB::rollback();

        return back()->with(
            'error',
            $e->getMessage()
        );
    }
}

public function checkReturn($id)
{
    $order = OfflineOrder::findOrFail($id);

    $start =
        Carbon::parse($order->start_date);

    $deadline =
        $start->addMonths($order->duration);

    $return =
        Carbon::parse($order->return_date);

    // CEK TERLAMBAT
    if ($return->gt($deadline))
    {
        $lateDays =
            $deadline->diffInDays($return);

        // contoh:
        $penalty = $lateDays * 50000;

        $order->update([

            'status' => 'pengembalian',

            'late_days' => $lateDays,

            'penalty' => $penalty
        ]);
    }
    else
{
    $order->update([
        'status' => 'pengembalian'
    ]);
}

    return redirect()
        ->route('offline-orders.show', $id);
}

public function storeAgreement(
    Request $request,
    $id
)
{
    $order = OfflineOrder::findOrFail($id);

    // ======================
    // VALIDASI
    // ======================

    $request->validate([

        'requirement_type' =>
        'required',

    'requirement_file' =>

        'required|
         mimes:pdf,jpg,jpeg,png|
         max:10240',

    'agreement_file' =>

        'required|
         mimes:pdf,jpg,jpeg,png|
         max:10240',

    'payment_proof' =>

        'nullable|
         mimes:pdf,jpg,jpeg,png|
         max:10240',

        'payment_amount' =>
            'required|numeric',

        'tax' => 'nullable|numeric|min:0',

        'admin_fee' => 'nullable|numeric|min:0',

        'shipping_fee' => 'nullable|numeric|min:0',

        'other_fee' => 'nullable|numeric|min:0',

    ]);

    // ======================
    // UPLOAD FILE
    // ======================

    $requirementFile =
        $request->file('requirement_file')
        ?->store(
            'offline-agreements',
            'public'
        );

    $agreementFile =
        $request->file('agreement_file')
        ?->store(
            'offline-agreements',
            'public'
        );

    $paymentProof =
        $request->file('payment_proof')
        ?->store(
            'offline-agreements',
            'public'
        );

    // ======================
    // SAVE
    // ======================

    $tax =
        $request->tax ?? 0;

    $adminFee =
        $request->admin_fee ?? 0;

    $shippingFee =
        $request->shipping_fee ?? 0;

    $otherFee =
        $request->other_fee ?? 0;

    $initialTotal =
        ($order->total_price ?? 0)
        + $tax
        + $adminFee
        + $shippingFee
        + $otherFee;

    OfflineAgreement::updateOrCreate(

        [
            'offline_order_id' => $order->id
        ],

        [

            // DOKUMEN
            'requirement_type' =>
                $request->requirement_type,

            'requirement_file' =>
                $requirementFile,

            // PERJANJIAN
            'agreement_file' =>
                $agreementFile,

            // PAYMENT
            // PAYMENT
'payment_type' =>
    $request->payment_type,

'payment_amount' =>
    $request->payment_amount,

'remaining_payment' =>

    $request->payment_type == 'dp'

    ? max(
        $initialTotal
      - $request->payment_amount
      , 0)

    : 0,

'tax' =>
    $tax,

'admin_fee' =>
    $adminFee,

'shipping_fee' =>
    $shippingFee,

'other_fee' =>
    $otherFee,

'payment_proof' =>
    $paymentProof,

            // NOTES
            'notes' =>
                $request->notes
        ]
    );

    // ======================
    // UPDATE STATUS
    // ======================

    $order->update([
        'status' => 'siap_disewakan'
    ]);

    return back()->with(
        'success',
        'Perjanjian berhasil disimpan'
    );
}

public function finalPayment(
    Request $request,
    $id
)
{
    DB::beginTransaction();

    try {

        $order = OfflineOrder::with([
            'agreement',
            'items'
        ])->findOrFail($id);

        // ======================
        // VALIDASI
        // ======================

        $request->validate([

            'payment_amount' =>
                'required|numeric',

            'final_payment_proof' =>

                'required|
                 mimes:pdf,jpg,jpeg,png|
                 max:10240',

        ]);

        // ======================
        // UPLOAD BUKTI
        // ======================

        $proof =
            $request->file(
                'final_payment_proof'
            )
            ?->store(
                'offline-agreements',
                'public'
            );

        // ======================
        // UPDATE PAYMENT
        // ======================

        $order->agreement->update([

    'payment_type' => 'lunas',

    'remaining_payment' => 0,

    'final_payment_proof' => $proof,

]);

        // ======================
        // TOTAL DENDA
        // ======================

       $totalPenalty = 0;

foreach ($order->items as $item)
{
    $totalPenalty +=
        ($item->repair_cost ?? 0);

    $totalPenalty +=
        ($item->lost_cost ?? 0);
}

$totalPenalty +=
    ($order->penalty ?? 0);

        // ======================
        // UPDATE STATUS
        // ======================

        if ($order->status != 'selesai') {
            $this->finalizeReturnStock($order);
        }

        $order->update([
            'status' => 'selesai'
        ]);

        DB::commit();

        return back()->with(
            'success',
            'Pelunasan berhasil'
        );

    } catch (\Exception $e) {

        DB::rollback();

        return back()->with(
            'error',
            $e->getMessage()
        );
    }
}

private function finalizeReturnStock(OfflineOrder $order): void
{
    foreach ($order->items as $item)
    {
        $product = Product::whereKey($item->product_id)
            ->lockForUpdate()
            ->firstOrFail();

        $returnedQty = (int) ($item->returned_qty ?? 0);
        $damagedQty = (int) ($item->damaged_qty ?? 0);
        $lostQty = (int) ($item->lost_qty ?? 0);

        $returnedToStock = max(0, $returnedQty - $damagedQty);
        $stockToRelease = min((int) $item->quantity, (int) $product->rented_stock);
        $stockToAvailable = min($returnedToStock, $stockToRelease);

        if ($stockToAvailable > 0)
        {
            $product->increment('available_stock', $stockToAvailable);
        }

        $remainingRelease = $stockToRelease - $stockToAvailable;
        $stockToMaintenance = min($damagedQty, $remainingRelease);

        if ($stockToMaintenance > 0)
        {
            $product->increment('maintenance_stock', $stockToMaintenance);

            Maintenance::create([
                'product_id' => $product->id,
                'qty' => $stockToMaintenance,
                'price' => $item->repair_cost ?? 0,
                'notes' => $item->return_notes
                    ?? 'Otomatis dari pengembalian ' . $order->order_code,
                'status' => 'proses',
            ]);
        }

        $remainingRelease -= $stockToMaintenance;
        $stockToLost = min($lostQty, $remainingRelease);

        if ($stockToLost > 0)
        {
            $product->decrement('total_stock', min($stockToLost, (int) $product->total_stock));
        }

        $releasedStock = $stockToAvailable + $stockToMaintenance + $stockToLost;

        if ($releasedStock > 0)
        {
            $product->decrement('rented_stock', $releasedStock);
        }
    }
}


public function show($id)
{
    $order = OfflineOrder::with([

        'items.product',
        'agreement'

    ])->findOrFail($id);

    return view(
        'admin.offline-orders.show',
        compact('order')
    );
}
}
