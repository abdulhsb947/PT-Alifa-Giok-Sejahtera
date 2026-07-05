<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index() {
    return view('home');
}

public function about() {
    return view('about');
}

public function contact() {
    return view('contact');
}

public function products() {
    $products = Product::all();
    return view('products', compact('products'));
}
}
