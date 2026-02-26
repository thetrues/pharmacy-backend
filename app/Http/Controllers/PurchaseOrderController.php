<?php

namespace App\Http\Controllers;

use App\Models\GoodsReceivedNote;
use App\Models\Inventory;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PurchaseOrderController extends Controller
{
    public function createPurchaseOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier' => 'required|exists:suppliers,id',
            'expectedDate' => 'required|date',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.cost_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();

        if(!$user){
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $po = PurchaseOrder::create([
            'order_date' => now(),
            'supplier_id' => $request->supplier,
            'expected_date' => $request->expectedDate,
            'total_amount' => collect($request->products)->sum(function ($item) {
                return $item['quantity'] * $item['cost_price'];
            }),
            'status' => 'pending',
            'created_by' => $user->id,
        ]);

        foreach ($request->products as $item) {
            $po->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['cost_price'],
                'total_price' => $item['quantity'] * $item['cost_price'],
            ]);
        }

        return response()->json(['message' => 'Purchase order created successfully', 'purchase_order' => $po], 201);
    }


    public function getPurchaseOrders(){
        $purchaseOrders = PurchaseOrder::with('items')->get();
        return response()->json(['purchase_orders' => $purchaseOrders], 200);
    }


    public function getPurchaseOrder($id)
    {
        $purchaseOrder = PurchaseOrder::with('items')->find($id);
        if (!$purchaseOrder) {
            return response()->json(['message' => 'Purchase order not found'], 404);
        }
        return response()->json(['purchase_order' => $purchaseOrder], 200);
    }

    public function todayPurchaseOrders()
    {
        $purchaseOrders = PurchaseOrder::with('items')
            ->whereDate('created_at', now()->toDateString())
            ->get();

        return response()->json(['purchase_orders' => $purchaseOrders], 200);
    }


    public function updatePurchaseOrderStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,received,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $purchaseOrder = PurchaseOrder::find($id);
        if (!$purchaseOrder) {
            return response()->json(['message' => 'Purchase order not found'], 404);
        }

        $purchaseOrder->status = $request->status;
        $purchaseOrder->save();

        return response()->json(['message' => 'Purchase order status updated successfully', 'purchase_order' => $purchaseOrder], 200);
    }

    public function deletePurchaseOrder($id)
    {
        $purchaseOrder = PurchaseOrder::find($id);
        if (!$purchaseOrder) {
            return response()->json(['message' => 'Purchase order not found'], 404);
        }
        $purchaseOrder->delete();
        return response()->json(['message' => 'Purchase order deleted successfully'], 200);
    }

    //grn
    public function generateGRN(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'received_date' => 'required|date',
            'received_by' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $purchaseOrder = PurchaseOrder::with('items')->find($request->purchase_order_id);
        if (!$purchaseOrder) {
            return response()->json(['message' => 'Purchase order not found'], 404);
        }

        if ($purchaseOrder->status !== 'received') {
            return response()->json(['message' => 'Cannot generate GRN for a purchase order that is not received'], 422);
        }

       /*'purchase_order_id',
        'received_date',
        'remarks',
        'received_by',*/

        $grn = GoodsReceivedNote::create([
            'purchase_order_id' => $request->purchase_order_id,
            'received_date' => $request->received_date,
            'remarks' => $request->remarks ?? null,
            'received_by' => $request->received_by ?? null,
        ]);

        /*   'grn_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
        */
        foreach ($request->items as $item) {
            $grn->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' =>$item['unit_price'] ?? 0,
                'total_price' => ($item['unit_price'] ?? 0) * $item['quantity'],
            ]);
            
            // Optionally, update inventory stock based on received items
          Inventory::create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'stock' => $item['quantity'],
                'added_by' => $request->received_by ?? null,
                'is_active' => true,
                'SKU' => 'SKU-' . uniqid(),
                'supplier' => $purchaseOrder->supplier_id,
                'batch_number' => 'BATCH-' . uniqid(),
                'expiry_date' => now()->addMonths(6), // Example expiry date
                'reorder_level' => 10,
                'cost_price' => $item['unit_price'] ?? 0,
                'selling_price' => ($item['unit_price'] ?? 0) * 1.2, // Example markup
            ]);
        }

        // Optionally, update the purchase order status to 'received' after generating the GRN
        $purchaseOrder->status = 'received';
        $purchaseOrder->save();

        return response()->json(['message' => 'GRN generated successfully', 'grn' => $grn], 200);
    }
}
