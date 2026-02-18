<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\error;

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

    public function importInventories(Request $request){
        $validator = Validator::make($request->all(), [
            'inventories' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $createdInventories = [];
        foreach ($request->inventories as $inventoryData) {
            $inventoryValidator = Validator::make($inventoryData, [
                'product_id' => 'required|integer|exists:products,id',
                'supplier' => 'required|string|max:255',
                'batch_number' => 'required|string|max:255',
                'expiry_date' => 'required|date',
                'reorder_level' => 'required|integer',
                'stock' => 'required|integer',
                'cost_price' => 'required|numeric',
                'selling_price' => 'required|numeric',
            ]);

            if ($inventoryValidator->fails()) {
               return response()->json(['errors' => $inventoryValidator->errors()], 422);
            }

            $sn = rand(501030, 102044);

            $createdInventories[] = Inventory::create([
                'product_id' => $inventoryData['product_id'],
                'supplier' => $inventoryData['supplier'],
                'batch_number' => $inventoryData['batch_number'],
                'expiry_date' => $inventoryData['expiry_date'],
                'reorder_level' => $inventoryData['reorder_level'],
                'stock' => $inventoryData['stock'],
                'quantity' => $inventoryData['stock'],
                'cost_price' => $inventoryData['cost_price'],
                'selling_price' => $inventoryData['selling_price'],
                'SKU' => 'SKU-'. $sn,
            ]);
        }

        return response()->json(['message' => count($createdInventories) . ' inventories imported', 'inventories' => $createdInventories]);
    }
}