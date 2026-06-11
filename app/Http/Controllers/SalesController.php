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
        $products = Product::where('is_active', true)->get()->map(function ($product) {
            // get all inventories for the product and get the last one with stock greater than 0 and expiry date greater than now
             $totalStock = Inventory::where('product_id', $product->id)
               // ->where('expiry_date', '>', now())
                ->sum('stock');
            return [
                'id' => $product->id,
                'name' => $product->name,
                'genericName' => $product->generic_name,
                'category' => $product->category,
                'price' => $product->inventories->last() ? (float) $product->inventories->last()->selling_price : 0,
                'stock' => $totalStock,
            ];
        });

        //all products where stock is greater than 0 and is active
        $products = $products->filter(function ($product) {
            return $product['stock'] > 0;
        })->values();

         return response()->json(['products' => $products]);
    }

   public function getSalesProducts1(Request $request){
    $perPage = $request->get('per_page', 15);
    $search = $request->get('search', '');
    
    $query = Product::where('is_active', true);
    
    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('generic_name', 'like', '%' . $search . '%')
              ->orWhere('category', 'like', '%' . $search . '%');
        });
    }
    
    $products = $query->get(['id', 'name', 'generic_name', 'category'])->map(function ($product) {
        // get all inventories for the product and get the last one with stock greater than 0 and expiry date greater than now
         $totalStock = Inventory::where('product_id', $product->id)
           // ->where('expiry_date', '>', now())
            ->sum('stock');
        return [
            'id' => $product->id,
            'name' => $product->name,
            'genericName' => $product->generic_name,
            'category' => $product->category,
            'price' => $product->inventories->last() ? (float) $product->inventories->last()->selling_price : 0,
            'stock' => $totalStock,
        ];
    });

    //all products where stock is greater than 0 and is active
    $products = $products->filter(function ($product) {
        return $product['stock'] > 0;
    })->values();

    $page = request('page', 1);
    $paginated = collect($products)->forPage($page, $perPage);
    $totalPages = ceil($products->count() / $perPage);

     return response()->json([
        'products' => $paginated,
        'total_pages' => $totalPages,
        'current_page' => $page,
        'next' => $page < $totalPages ? $page + 1 : null,
        'previous' => $page > 1 ? $page - 1 : null,
     ]);
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
