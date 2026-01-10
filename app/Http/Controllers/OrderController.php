<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Order\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function createOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'order_items' => 'required|array',
            'order_items.*.product_id' => 'required|exists:products,id',
            'order_items.*.quantity' => 'required|integer|min:1',
            'order_items.*.unit_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        
        $existingOrdersCount = Order::whereDate('created_at', now()->toDateString())->count();
        $order_number = 'ORD-' . now()->format('Ymd') . '-' . str_pad($existingOrdersCount + 1, 4, '0', STR_PAD_LEFT);

        $order = Order::create([
            'order_number' => $order_number,    
            'customer_id' => $request->customer_id,
            'total_amount' => collect($request->order_items)->sum(function ($item) {
                return $item['quantity'] * $item['unit_price'];
            }),
            'status' => 'pending',
        ]);

        foreach ($request->order_items as $item) {
            $order->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['quantity'] * $item['unit_price'],
            ]);

            $this->decreaseInventory($item['product_id'], $item['quantity']);
        }

        return response()->json(['message' => 'Order created successfully', 'order' => $order], 201);
    }

    
    public function decreaseInventory($productId, $quantity)
            {
                // Get all inventory batches for the product, ordered by earliest expiry
                $batches = Inventory::where('product_id', $productId)
                    ->where('stock', '>', 0)
                    ->where('expiry_date', '>', now())
                    ->orderBy('expiry_date', 'asc')
                    ->get();

                $remaining = $quantity;

                foreach ($batches as $batch) {
                    if ($remaining <= 0) break;

                    $deduct = min($batch->stock, $remaining);
                    $batch->stock -= $deduct;
                    $batch->save();

                    $remaining -= $deduct;
                }

                if ($remaining > 0) {
                    // Not enough stock available
                    throw new \Exception('Insufficient stock for product ID: ' . $productId);
                }
            }

    public function getOrders(){
        $orders = Order::with('items')->get();
        return response()->json(['orders' => $orders], 200);
    }

    public function getOrder($id)
    {
        $order = Order::with('items')->find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        return response()->json(['order' => $order], 200);
    }


    public function todayOrders()
    {
        $orders = Order::with('items')
            ->whereDate('created_at', now()->toDateString())
            ->get();
        return response()->json(['orders' => $orders], 200);
    }
}
