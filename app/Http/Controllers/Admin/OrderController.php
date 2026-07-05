<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Agreement;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // ======================
    // LIST ORDER
    // ======================
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.product']);

        // 🔍 SEARCH
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('project_name', 'like', '%' . $request->search . '%')
                    ->orWhere('order_code', 'like', '%' . $request->search . '%')
                    ->orWhereHas('user', function ($u) use ($request) {
                        $u->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        // 🎯 FILTER STATUS
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->get();

        return view('admin.orders.index', compact('orders'));
    }

    // ======================
    // DETAIL
    // ======================
    public function show($id)
{
    $order = Order::with([
        'user',
        'items.product',
        'messages',
        'agreement'
    ])->findOrFail($id);

    // Ambil agreement dari database
    $agreement = Agreement::where('order_id', $order->id)->first();

    return view(
        'admin.orders.show',
        compact(
            'order',
            'agreement'
        )
    );
}


public function reviewLapangan(Request $request, $id)
{
    $order = Order::findOrFail($id);

    if ($order->status != 'menunggu_hasil_survey') {
        return back()->with('error', 'Survey lapangan hanya bisa dikirim untuk order yang menunggu hasil survey');
    }

    $validated = $request->validate([
        'review_document' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        'admin_notes' => ['nullable', 'string', 'max:5000'],
    ]);

    if (!$request->hasFile('review_document') && blank($validated['admin_notes'] ?? null)) {
        return back()->with('error', 'Isi hasil survey lapangan atau upload dokumen PDF');
    }

    $data = [
        'status' => 'review_lapangan',
        'admin_notes' => $validated['admin_notes'] ?? null,
    ];

    if ($request->hasFile('review_document')) {
        $data['review_document'] = $request
            ->file('review_document')
            ->store('review-documents', 'public');
    }

    $order->update($data);

    Notification::create([
        'user_id' => $order->user_id,
        'title' => 'Review Lapangan',
        'message' => 'Silakan lihat hasil survey lapangan dan revisi pesanan',
        'type' => 'order',
        'is_read' => 0,
        'url' => route('customer.orders')
    ]);

    return back()->with(
        'success',
        'Hasil survey lapangan berhasil dikirim'
    );
}

    // ======================
    // APPROVE ORDER
    // ======================
    

    public function approve(Request $request, $id)
{
    DB::beginTransaction();

    try {

        $order = Order::whereKey($id)
            ->lockForUpdate()
            ->firstOrFail();

        $order->load('items.product');

        // ======================
        // CEK STATUS (ANTI DOUBLE)
        // ======================
        if (!in_array($order->status, ['menunggu_verifikasi', 'review_lapangan'])) {
            throw new \Exception('Order sudah diproses');
        }

        // ======================
        // CEK STOK DULU
        // ======================
        foreach ($order->items as $item) {

            $product = Product::whereKey($item->product_id)
                ->lockForUpdate()
                ->first();

            if (!$product) {
                throw new \Exception('Produk tidak ditemukan');
            }

            if ($item->quantity > $product->available_stock) {
                throw new \Exception('Stok tidak cukup untuk ' . $product->name);
            }
        }

        // ======================
        // KURANGI STOK
        // ======================
        foreach ($order->items as $item) {

            $product = Product::whereKey($item->product_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($item->quantity > $product->available_stock) {
                throw new \Exception('Stok tidak cukup untuk ' . $product->name);
            }

            $product->decrement('available_stock', $item->quantity);
            $product->increment('rented_stock', $item->quantity);
        }

        // ======================
        // UPDATE ORDER
        // ======================
        $order->update([
            'status' => 'disetujui',
            'admin_notes' => $request->notes ?: $order->admin_notes
        ]);

        // ======================
        // AGREEMENT
        // ======================
        Agreement::updateOrCreate(
            ['order_id' => $order->id],
            [
                'status' => 'menunggu_persetujuan_pelanggan'
            ]
        );

        // ======================
        // NOTIFIKASI CUSTOMER
        // ======================
        Notification::create([
            'user_id' => $order->user_id,
            'title' => 'Pesanan Disetujui',
            'message' => 'Pesanan ' . $order->order_code . ' telah disetujui oleh admin',
            'type' => 'order',
            'is_read' => 0,
            'url' => route('customer.orders')
        ]);

        DB::commit();

        return back()->with('success', 'Order disetujui & stok dikurangi');

    } catch (\Exception $e) {

        DB::rollback();

        return back()->with('error', $e->getMessage());
    }
} 

    // ======================
    // REJECT ORDER
    // ======================
    public function reject(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if (!in_array($order->status, ['menunggu_verifikasi', 'review_lapangan'])) {
            return back()->with('error', 'Order sudah diproses');
        }

        $order->update([
            'status' => 'ditolak',
            'admin_notes' => $request->admin_notes
        ]);

        Notification::create([
            'user_id' => $order->user_id,
            'title' => 'Pesanan Ditolak',
            'message' => 'Pesanan ' . $order->order_code . ' ditolak oleh admin',
            'type' => 'order',
            'is_read' => 0,
            'url' => route('customer.orders')
        ]);

        return back()->with('error', 'Pesanan ditolak');
    }

    public function updateCost(Request $request, $id)
{
    $request->validate([
        'tax' => 'nullable|numeric|min:0',
        'transportation_cost' => 'nullable|numeric|min:0',
        'installation_dismantling_cost' => 'nullable|numeric|min:0',
        'other_cost' => 'nullable|numeric|min:0',
    ]);

    $order = Order::findOrFail($id);

    $order->update([
        'tax' => $request->tax ?? 0,
        'transportation_cost' => $request->transportation_cost ?? 0,
        'installation_dismantling_cost' => $request->installation_dismantling_cost ?? 0,
        'other_cost' => $request->other_cost ?? 0,
    ]);

    return back()->with(
        'success',
        'Biaya tambahan berhasil disimpan'
    );
}

}
