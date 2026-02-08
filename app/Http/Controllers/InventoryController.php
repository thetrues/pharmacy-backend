<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InventoryController extends Controller
{
    public function getInventories(){
        $inventories = Inventory::with('product')->get();
        //add status based on stock and expiry
        $inventories->transform(function ($inventory) {
            if ($inventory->stock <= 0) {
                $inventory->status = 'out-of-stock';
            } elseif ($inventory->expiry_date <= now()->addDays(7)) {
                $inventory->status = 'expiring';
            } else {
                $inventory->status = 'in-stock';
            }
            return $inventory;
        });
        return response()->json(['inventories' => $inventories]);
    }

    public function getInventory($id){
        $inventory = Inventory::with('product')->find($id);
        if(!$inventory){
            return response()->json(['message' => 'Inventory not found'], 404);
        }
        return response()->json(['inventory' => $inventory]);
    }

    public function deleteInventory($id){
        $inventory = Inventory::find($id);
        if(!$inventory){
            return response()->json(['message' => 'Inventory not found'], 404);
        }
        $inventory->delete();
        return response()->json(['message' => 'Inventory deleted']);
    }

    public function updateInventory(Request $request, $id){
        $inventory = Inventory::find($id);
        if(!$inventory){
            return response()->json(['message' => 'Inventory not found'], 404);
        }
        $validated = $request->validate([
            'product_id' => 'sometimes|integer|exists:products,id',
            'quantity' => 'sometimes|integer',
            'SKU' => 'sometimes|string|max:255',
            'supplier' => 'sometimes|string|max:255',
            'batch_number' => 'sometimes|string|max:255',
            'expiry_date' => 'sometimes|date',
            'reorder_level' => 'sometimes|integer',
            'stock' => 'sometimes|integer',
            'cost_price' => 'sometimes|numeric',
            'selling_price' => 'sometimes|numeric',
            'is_active' => 'sometimes|boolean',
            'added_by' => 'sometimes|integer|exists:users,id',
        ]);

        $inventory->update($validated);
        return response()->json(['message' => 'Inventory updated', 'inventory' => $inventory]);
    }

    public function createInventory(Request $request){
        $validator = Validator::make($request->all(), [
            'productId' => 'required|integer|exists:products,id',
            'supplier' => 'required|string|max:255',
            'batchNumber' => 'required|string|max:255',
            'expiryDate' => 'required|date',
            'reorderLevel' => 'required|integer',
            'stock' => 'required|integer',
            'costPrice' => 'required|numeric',
            'sku' => 'required|string|max:255',
            'sellingPrice' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $inventory = Inventory::create([
            'product_id' => $request->productId,
            'supplier' => $request->supplier,
            'batch_number' => $request->batchNumber,
            'expiry_date' => $request->expiryDate,
            'reorder_level' => $request->reorderLevel,
            'stock' => $request->stock,
            'quantity' => $request->stock,
            'cost_price' => $request->costPrice,
            'selling_price' => $request->sellingPrice,
            'SKU' => $request->sku,
        ]);
        return response()->json(['message' => 'Inventory created', 'inventory' => $inventory]);
    }
}