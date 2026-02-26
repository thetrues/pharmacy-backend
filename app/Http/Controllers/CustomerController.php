<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer\Customer;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function getCustomers()
    {
       $customers = Customer::all();
       return response()->json($customers);
    }

    public function getCustomer($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }
        return response()->json($customer, 200);
    }

    public function deleteCustomer($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }
        $customer->delete();
        return response()->json(['message' => 'Customer deleted successfully'], 200);
    }

    public function updateCustomer(Request $request, $id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255|unique:customers,email,' . $id,
            'phone' => 'sometimes|nullable|string|max:20',
            'address' => 'sometimes|nullable|string|max:500',
            'credit_limit' => 'sometimes|nullable|numeric|min:0',
            'current_credit' => 'sometimes|nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $customer->update($validator->validated());

        return response()->json(['message' => 'Customer updated successfully', 'customer' => $customer], 200);
    }

    public function createCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'credit_limit' => 'nullable|numeric|min:0',
            'current_credit' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $currentCredit = $request->input('current_credit', 0);

        $customer = Customer::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'credit_limit' => $request->input('credit_limit', 0),
            'current_credit' => $currentCredit,
        ]);

        return response()->json(['message' => 'Customer created successfully', 'customer' => $customer], 201);
    }
}