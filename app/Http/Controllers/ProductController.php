<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\View\View;

class ProductController extends Controller
{

    public function index(): View
    {
        $products = Product::query()->paginate();

        return view('products.index', compact('products'));
    }
}
