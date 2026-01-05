<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function getInventories(){
        $inventories = Inventory::with('product')->get();
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
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer',
            'SKU' => 'required|string|max:255',
            'supplier' => 'required|string|max:255',
            'batch_number' => 'required|string|max:255',
            'expiry_date' => 'required|date',
            'reorder_level' => 'required|integer',
            'stock' => 'required|integer',
            'cost_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'is_active' => 'required|boolean',
            'added_by' => 'required|integer|exists:users,id',
        ]);

        $inventory = Inventory::create($validated);
        return response()->json(['message' => 'Inventory created', 'inventory' => $inventory]);
    }
}