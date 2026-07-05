<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\AdminNotification;

class OrderController extends Controller
{

    // ===============================
    // 📌 LIST ORDER
    // ===============================
    public function index(Request $request)
    {
        $query = Order::withCount('items')
        ->where('user_id', auth()->id());

        // 🔍 SEARCH
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('order_code', 'like', '%' . $request->search . '%')
                    ->orWhere('project_name', 'like', '%' . $request->search . '%');
            });
        }

        // 🔥 FILTER STATUS
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->get();

        return view('customer.orders', compact('orders'));
    }


    // ===============================
    // 📌 FORM ORDER
    // ===============================
    public function create()
    {
        $products = Product::all();
        return view('customer.new-order', compact('products'));
    }


    // ===============================
    // 📌 STEP 2 (PROJECT)
    // ===============================
    public function step2(Request $request)
    {
        if (!$request->project_name || !$request->project_location) {
            return back()->with('error', 'Nama proyek dan lokasi wajib diisi');
        }
        $phone = $request->phone ?? auth()->user()->phone;

        session([
            'order.project_name' => $request->project_name,
            'order.project_location' => $request->project_location,
            'order.phone' => $phone,
        ]);

        $products = Product::all();

        return view('customer.new-order-step2', compact('products'));
    }

    public function step2View()
{
    $products = Product::all();
    return view('customer.new-order-step2', compact('products'));
}


    // ===============================
    // 📌 STEP 3 (PRODUK)
    // ===============================
    public function step3(Request $request)
    {
        if (!$request->products) {
            return back()->with('error', 'Pilih minimal 1 produk');
        }

        foreach ($request->products as $id) {
            if (empty($request->quantities[$id])) {
                return back()->with('error', 'Jumlah produk wajib diisi');
            }
        }

        if (!$request->start_date || !$request->duration) {
            return back()->with('error', 'Tanggal dan durasi wajib diisi');
        }

        $today = Carbon::today();
$startDate = Carbon::parse($request->start_date);

if ($startDate->lt($today)) {
    return back()->with('error', 'Tanggal tidak boleh sebelum hari ini');
}

        session([
            'order.products' => $request->products,
            'order.quantities' => $request->quantities,
            'order.start_date' => $request->start_date,
            'order.duration' => $request->duration,
            'order.duration_unit' => 'bulan',
            'order.notes' => $request->notes
        ]);

        return view('customer.new-order-step3');
    }


    // ===============================
    // 📌 STEP 4 (UPLOAD + REVIEW)
    // ===============================
    public function step4(Request $request)
    {
        if (!$request->hasFile('document')) {
            return back()->with('error', 'Dokumen wajib diupload');
        }

        $file = $request->file('document');
        $path = $file->store('documents', 'public');

        session([
            'order.document_type' => $request->document_type,
            'order.document' => $path,
        ]);

        return view('customer.new-order-step4');
    }


    // ===============================
    // 📌 SIMPAN ORDER
    // ===============================
    public function store(Request $request)
    {
        if (!session()->has('order.products')) {
            return back()->with('error', 'Sesi habis, silakan ulangi pemesanan');
        }

        DB::beginTransaction();

        try {

            // 🔥 BUAT ORDER
            $order = Order::create([
                'order_code' => 'ORD-' . rand(1000, 9999),
                'user_id' => auth()->id(),

                'project_name' => session('order.project_name'),
                'project_location' => session('order.project_location'),
                'phone' => session('order.phone'),

                'status' => 'menunggu_hasil_survey',

                'start_date' => session('order.start_date'),
                'duration' => session('order.duration'),
                'duration_unit' => session('order.duration_unit'),

                'notes' => session('order.notes'),
                'document' => session('order.document'),
            ]);

            $quantities = session('order.quantities');

            // 🔥 SIMPAN ITEM
            foreach (session('order.products') as $productId) {

                $product = Product::findOrFail($productId);

                $qty = (int) $quantities[$productId];

                if ($qty <= 0) {
                    throw new \Exception('Jumlah tidak valid');
                }

                if ($qty > $product->available_stock) {
                    throw new \Exception('Stok tidak cukup untuk ' . $product->name);
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $qty,
                    'price' => $product->price_per_month
                ]);

               /* // 🔥 UPDATE STOK
                $product->decrement('available_stock', $qty);
                $product->increment('rented_stock', $qty); */
            }

            DB::commit();

            AdminNotification::create([
    'title' => 'Pesanan Baru',
    'message' => $order->order_code . ' menunggu verifikasi',
    'url' => '/admin/orders/' . $order->id,
    'is_read' => 0
]);

            // 🔥 HAPUS SESSION
            session()->forget('order');

            return redirect('/customer/orders')
                ->with('success', 'Pesanan berhasil dibuat');
        } catch (\Exception $e) {

            DB::rollback();

            return back()->with('error', $e->getMessage());
        }
    }


    // ===============================
    // 📌 DETAIL ORDER
    // ===============================
    public function show($id)
{
    $order = Order::with('items.product')->findOrFail($id);

    return view(
        'customer.order-detail',
        compact('order')
    );
}

    public function edit($id)
    {
        $order = Order::findOrFail($id);

        // ❗ hanya boleh edit jika ditolak
        if ($order->status != 'review_lapangan'
        && $order->status != 'ditolak') {
            return back()->with('error', 'Pesanan tidak bisa diedit');
        }

        // 🔥 isi session dengan data lama
        session([
            'order.edit_id' => $order->id,
            'order.project_name' => $order->project_name,
            'order.project_location' => $order->project_location,
            'order.phone' => $order->phone,
            'order.start_date' => $order->start_date,
            'order.duration' => $order->duration,
            'order.duration_unit' => $order->duration_unit,
            'order.notes' => $order->notes,
            'order.document' => $order->document,
        ]);

        // 🔥 ambil item produk
        $products = $order->items;

        $productIds = [];
        $quantities = [];

        foreach ($products as $item) {
            $productIds[] = $item->product_id;
            $quantities[$item->product_id] = $item->quantity;
        }

        session([
            'order.products' => $productIds,
            'order.quantities' => $quantities
        ]);

        return redirect('/customer/orders/create');
    }

    public function update(Request $request, $id)
    {
        if (!session()->has('order.products')) {
            return back()->with('error', 'Sesi habis, silakan ulangi');
        }

        DB::beginTransaction();

        try {

            $order = Order::findOrFail($id);

            // ❗ hanya boleh edit jika ditolak
            if ($order->status != 'review_lapangan'
            && $order->status != 'ditolak') {
                return back()->with('error', 'Pesanan tidak bisa diedit');
            }

            // 🔥 UPDATE DATA ORDER
            $order->update([
                'project_name' => session('order.project_name'),
                'project_location' => session('order.project_location'),
                'order.phone' => session('order.phone'),
                'start_date' => session('order.start_date'),
                'duration' => session('order.duration'),
                'duration_unit' => session('order.duration_unit'),
                'notes' => session('order.notes'),
                'document' => session('order.document'),

                // 🔥 reset ke awal
                'status' => 'menunggu_verifikasi'
            ]);

            // 🔥 HAPUS ITEM LAMA
            OrderItem::where('order_id', $order->id)->delete();

            // 🔥 SIMPAN ITEM BARU
            $quantities = session('order.quantities');

            foreach (session('order.products') as $productId) {

                $product = Product::findOrFail($productId);

                $qty = (int) $quantities[$productId];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $qty,
                    'price' => $product->price_per_month
                ]);
            }

            DB::commit();

            AdminNotification::create([
    'title' => 'Revisi Pesanan',
    'message' => $order->order_code . ' telah direvisi customer',
    'url' => '/admin/orders/' . $order->id,
    'is_read' => 0
]);

            session()->forget('order');

            return redirect('/customer/orders')
                ->with('success', 'Pesanan berhasil diperbarui');
        } catch (\Exception $e) {

            DB::rollback();

            return back()->with('error', $e->getMessage());
        }
    }
}
