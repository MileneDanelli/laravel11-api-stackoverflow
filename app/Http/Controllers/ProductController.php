<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    function index() {
        return Product::all();
    }

    function show($id) {
        return Product::find($id);
    }

    function store(Request $request) {
        $product = Product::create($request->only('title', 'image'));

        return response($product, Response::HTTP_CREATED);
    }

    function update(Request $request, $id) {
        $product = Product::find($id);
        $product->update($request->only('title', 'image'));

        return response($product, Response::HTTP_ACCEPTED);
    }

    function destroy($id) {
        Product::destroy($id);

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
