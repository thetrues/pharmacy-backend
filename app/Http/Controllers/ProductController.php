<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function getProducts(){
        $products = Product::with('inventories')->get()->map(function ($product) {
            $lastInventory = $product->inventories->sortByDesc('id')->first();
            $product->price = $lastInventory ? (float) $lastInventory->price : 0;
            return $product;
        });
        return response()->json(['products' => $products]);
    }

    public function getProduct($id){
        $product = Product::with('inventories')->find($id);
        if(!$product){
            return response()->json(['message' => 'Product not found'], 404);
        }
        return response()->json(['product' => $product]);
    }

    public function deleteProduct($id){
        $product = Product::find($id);
        if(!$product){
            return response()->json(['message' => 'Product not found'], 404);
        }
        $product->delete();
        return response()->json(['message' => 'Product deleted']);
    }

    public function updateProduct(Request $request, $id){
        $product = Product::find($id);
        if(!$product){
            return response()->json(['message' => 'Product not found'], 404);
        }
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'generic_name' => 'sometimes|string',
            'dosage_type' => 'sometimes|string',
            'category' => 'sometimes|string',
            'strength' => 'sometimes|string',
            'threshold' => 'sometimes|integer',
            'is_prescription' => 'sometimes|boolean',
            'stock' => 'sometimes|integer',
            'is_active' => 'sometimes|boolean',
        ]);

        $product->update($validated);
        return response()->json(['message' => 'Product updated', 'product' => $product]);
    }

    public function createProduct(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'generic_name' => 'nullable|string',
            'dosage_type' => 'required|string',
            'category' => 'required|string',
            'strength' => 'required|string',
            'threshold' => 'required|integer',
            'is_prescription' => 'sometimes|boolean',
            'stock' => 'sometimes|integer',
            'is_active' => 'sometimes|boolean',
        ]);

        $product = Product::create($validated);
        return response()->json(['message' => 'Product created', 'product' => $product]);
    }

    public function importProducts(Request $request){
        $validator = Validator::make($request->all(), [
            'products' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $productsData = $request->input('products');
        $createdProducts = [];
        foreach ($productsData as $productData) {
            $validator = Validator::make($productData, [
                'name' => 'required|string|max:255',
                'generic_name' => 'nullable|string',
                'dosage_type' => 'required|string',
                'category' => 'required|string',
                'strength' => 'required|string',
                'threshold' => 'required|integer',
                'is_prescription' => 'sometimes|boolean',
                'stock' => 'sometimes|integer',
                'is_active' => 'sometimes|boolean',
            ]);

            if ($validator->fails()) {
                continue; // Skip invalid product data
            }

            $createdProducts[] = Product::create($validator->validated());
        }
        return response()->json(['message' => 'Products imported', 'products' => $createdProducts]);
    }
}
