<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SalesController extends Controller
{

   public function getSalesProducts(){
    // $inventories = Inventory::with('product')->get();

     // { id: "1", name: "Paracetamol 500mg", genericName: "Acetaminophen", price: 5.00, stock: 245, category: "Pain Relief" },

    $products = Product::where('is_active', true)->get(['id', 'name', 'generic_name', 'category'])->map(function ($product) {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'genericName' => $product->generic_name,
            'category' => $product->category,
            'price' => $product->inventories->last() ? (float) $product->inventories->last()->selling_price : 0,
            'stock' => $product->inventories->last() ? $product->inventories->last()->stock : 0,
        ];
    });

     /*$products = $inventories->map(function ($inventory) {
         return [
             'id' => $inventory->product_id,
             'name' => $inventory->product->name,
             'genericName' => $inventory->product->generic_name,
             'price' => $inventory->selling_price,
             'stock' => $inventory->stock,
             'category' => $inventory->product->category,
         ];
     });*/
     return response()->json(['products' => $products]);
   }

   public function saleCreate(Request $request){
        $validator = Validator::make($request->all(), [
            'orderNumber' => 'required|unique:sales,orderNumber',
            'customer_id' => 'nullable|exists:customers,id',
            'customerName' => 'nullable|string',
            'subtotal' => 'required|numeric',
            'tax' => 'required|numeric',
            'total' => 'required|numeric',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $user = Auth::user();
        $sale = Sales::create([
            'orderNumber' => $request->orderNumber,
            'customer_id' => $request->customer_id,
            'customerName' => $request->customerName,
            'subtotalAmount' => $request->subtotal,
            'taxAmount' => $request->tax,
            'totalAmount' => $request->total,
            'created_by' => $user->id
        ]);

        foreach ($request->items as $item) {
            $sale->items()->create([
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'unitPrice' => $item['price'],
            ]);
        }


        return response()->json(['message' => 'Sale created successfully', 'sale' => $sale]);
   }

    public function getSalesReport(Request $request)
    {
        // This is a placeholder for the sales report logic
        // You can implement the logic to fetch and return sales data based on the request parameters
        return response()->json(['message' => 'Sales report functionality is not implemented yet']);
    }

    public function getDailySales(Request $request)
    {
        // This is a placeholder for the daily sales logic
        // You can implement the logic to fetch and return daily sales data based on the request parameters
        return response()->json(['message' => 'Daily sales functionality is not implemented yet']);
    }

    public function getMonthlySales(Request $request)
    {
        // This is a placeholder for the monthly sales logic
        // You can implement the logic to fetch and return monthly sales data based on the request parameters
        return response()->json(['message' => 'Monthly sales functionality is not implemented yet']);
    }
}
