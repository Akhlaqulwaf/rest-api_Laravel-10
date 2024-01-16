<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductCreateRequest;
use App\Models\Products;
use Illuminate\Http\JsonResponse;

class ProductsController extends Controller
{
    public function create(ProductCreateRequest $request): JsonResponse
    {
        $data = $request->validated();

        $product = new Products($data);
        $product->save();

        return response()->json($product, 201);
    }

    public function get():JsonResponse{
        $products = Products::all();

        return response()->json($products, 200);
    }
}
