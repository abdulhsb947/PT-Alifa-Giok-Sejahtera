<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\AdminNotification;
use App\Models\OfflineOrder;
use App\Models\Order;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // ======================
    // LIST PRODUK
    // ======================
    public function index()
    {
        $products = Product::all();
        return view('admin.products.index', compact('products'));
    }

    // ======================
    // FORM TAMBAH PRODUK
    // ======================
    public function create()
    {
        return view('admin.products.create');
    }

    // ======================
    // SIMPAN PRODUK
    // ======================
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price_per_month' => 'required|numeric',
            'total_stock' => 'required|numeric'
        ]);

        // 🔥 PROSES UPLOAD GAMBAR
        $pathGambar = null;

        if ($request->hasFile('image')) {
            $pathGambar = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'price_per_month' => $request->price_per_month,
            'total_stock' => $request->total_stock,
            'available_stock' => $request->total_stock,
            'rented_stock' => 0,
            'maintenance_stock' => 0,
            'image' => $pathGambar
        ]);

        return redirect('/admin/products')->with('success', 'Produk berhasil ditambahkan');
    }

    // ======================
    // FORM EDIT PRODUK
    // ======================
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('admin.products.edit', compact('product'));
    }

    // ======================
    // UPDATE PRODUK
    // ======================
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // 🔥 VALIDASI
        $request->validate([
            'name' => 'required',
            'price_per_month' => 'required|numeric',
            'total_stock' => 'required|numeric|min:0'
        ]);

        // 🔥 HITUNG STOK YANG SEDANG DIGUNAKAN
        $stokTerpakai = $product->rented_stock + $product->maintenance_stock;

        // 🔥 HITUNG STOK TERSEDIA BARU
        $stokTersedia = $request->total_stock - $stokTerpakai;

        if ($stokTersedia < 0) {
            return back()->with('error', 'Total stok tidak boleh kurang dari stok yang sedang digunakan');
        }

        // 🔥 UPDATE GAMBAR
        $pathGambar = $product->image;

        if ($request->hasFile('image')) {

            // hapus gambar lama
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $pathGambar = $request->file('image')->store('products', 'public');
        }

        // 🔥 UPDATE DATA PRODUK
        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'price_per_month' => $request->price_per_month,
            'total_stock' => $request->total_stock,
            'available_stock' => $stokTersedia,
            'image' => $pathGambar
        ]);

        return redirect('/admin/products')->with('success', 'Produk berhasil diperbarui');
    }

    // ======================
    // HAPUS PRODUK
    // ======================
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        $activeOrderCount = Order::whereNotIn('status', [
                'selesai',
                'ditolak'
            ])
            ->whereHas('items', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->count();

        $activeRentalCount = Rental::where('status', '!=', 'selesai')
            ->whereHas('order.items', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->count();

        $activeOfflineOrderCount = OfflineOrder::where('status', '!=', 'selesai')
            ->whereHas('items', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->count();

        $isProductInUse =
            $product->rented_stock > 0 ||
            $product->maintenance_stock > 0 ||
            $activeOrderCount > 0 ||
            $activeRentalCount > 0 ||
            $activeOfflineOrderCount > 0;

        if ($isProductInUse) {
            $message =
                $product->name .
                ' tidak bisa dihapus karena masih terkait dengan ' .
                $activeOrderCount . ' pemesanan online aktif, ' .
                $activeRentalCount . ' penyewaan online aktif, ' .
                $activeOfflineOrderCount . ' pemesanan/penyewaan offline aktif, ' .
                $product->rented_stock . ' stok disewa, dan ' .
                $product->maintenance_stock . ' stok perawatan. ' .
                'Selesaikan semua status terlebih dahulu.';

            try {
                AdminNotification::create([
                    'title' => 'Produk Tidak Bisa Dihapus',
                    'message' => $message,
                    'url' => '/admin/products',
                    'is_read' => 0,
                ]);
            } catch (\Throwable $e) {
                report($e);
            }

            return back()->with(
                'error',
                $message
            );
        }

        $product->delete();

        return back()->with('success', 'Produk berhasil dihapus');
    }

    // ======================
    // PRODUK UNTUK PUBLIC
    // ======================
    public function publicProducts()
    {
        $products = Product::all();
        return view('public.products', compact('products'));
    }

    // ======================
    // PRODUK UNTUK CUSTOMER
    // ======================
    public function customerProducts()
    {
        $products = Product::all();
        return view('customer.products', compact('products'));
    }

    // ======================
    // HITUNG STOK MAINTENANCE
    // ======================
    public function getMaintenanceStockAttribute()
    {
        return $this->maintenances()
            ->where('status', 'proses')
            ->sum('qty');
    }
}
