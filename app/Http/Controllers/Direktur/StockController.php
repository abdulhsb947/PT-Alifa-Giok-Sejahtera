<?php

namespace App\Http\Controllers\Direktur;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        $products = Product::all();

        // statistik global
        $totalStock = $products->sum('available_stock');
        $totalRented = $products->sum('rented_stock');

        return view('direktur.stock', compact('products', 'totalStock', 'totalRented'));
    }
}
