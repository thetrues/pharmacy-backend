<?php

namespace App\Http\Controllers;

use App\Models\Suppliers;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function getSuppliers()
    {
        $suppliers = Suppliers::all();
        return response()->json(['suppliers' => $suppliers]);
    }

    public function getSupplier($id)
    {
        $supplier = Suppliers::find($id);
        if (!$supplier) {
            return response()->json(['message' => 'Supplier not found'], 404);
        }
        return response()->json(['supplier' => $supplier], 200);
    }

    public function deleteSupplier($id)
    {
        $supplier = Suppliers::find($id);
        if (!$supplier) {
            return response()->json(['message' => 'Supplier not found'], 404);
        }
        $supplier->delete();
        return response()->json(['message' => 'Supplier deleted successfully'], 200);
    }

    public function updateSupplier(Request $request, $id)
    {
        $supplier = Suppliers::find($id);
        if (!$supplier) {
            return response()->json(['message' => 'Supplier not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'contact_person' => 'sometimes|nullable|string|max:255',
            'phone' => 'sometimes|nullable|string|max:20',
            'email' => 'sometimes|nullable|email|max:255',
            'address' => 'sometimes|nullable|string|max:500',
            'bank_account' => 'sometimes|nullable|string|max:255',
        ]);

        $supplier->update($validated);

        return response()->json(['message' => 'Supplier updated successfully', 'supplier' => $supplier], 200);
    }

    public function createSupplier(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'bank_account' => 'nullable|string|max:255',
        ]);

        //name, contact_person, phone, email, address, bank_account

        $supplier = Suppliers::create($validated);

        return response()->json(['message' => 'Supplier created successfully', 'supplier' => $supplier], 201);
    }
}
