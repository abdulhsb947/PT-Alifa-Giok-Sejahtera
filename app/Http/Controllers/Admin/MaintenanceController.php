<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Maintenance;
use Illuminate\Support\Facades\DB;

class MaintenanceController extends Controller
{
    public function index()
    {
        $maintenances = Maintenance::with('product')->latest()->get();
        $products = Product::all();

        return view('admin.maintenance', compact('maintenances', 'products'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $product = Product::whereKey($request->product_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($request->qty > $product->available_stock) {
                throw new \Exception('Stok tidak cukup');
            }

            $product->decrement('available_stock', $request->qty);
            $product->increment('maintenance_stock', $request->qty);

            Maintenance::create([
                'product_id' => $product->id,
                'qty' => $request->qty,
                'price' => $request->price,
                'notes' => $request->notes,
                'status' => 'proses'
            ]);

            DB::commit();

            return back()->with('success', 'Produk masuk perawatan');

        } catch (\Exception $e) {

            DB::rollback();

            return back()->with('error', $e->getMessage());
        }
    }

    public function selesai($id)
    {
        DB::beginTransaction();

        try {

            $maintenance = Maintenance::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($maintenance->status == 'selesai') {
                DB::commit();

                return back()->with('error', 'Perawatan sudah selesai');
            }

            $product = Product::whereKey($maintenance->product_id)
                ->lockForUpdate()
                ->firstOrFail();

            $stockToReturn =
                min(
                    (int) $maintenance->qty,
                    (int) $product->maintenance_stock
                );

            if ($stockToReturn > 0) {
                $product->increment('available_stock', $stockToReturn);
                $product->decrement('maintenance_stock', $stockToReturn);
            }

            $maintenance->update([
                'status' => 'selesai'
            ]);

            DB::commit();

            return back()->with('success', 'Perawatan selesai');

        } catch (\Exception $e) {

            DB::rollback();

            return back()->with('error', $e->getMessage());
        }
    }
}
