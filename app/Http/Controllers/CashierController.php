<?php

namespace App\Http\Controllers;

use App\Models\Order\Order;
use App\Models\CashierShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CashierController extends Controller
{
    public function startShift(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cashier_id' => 'required|exists:users,id',
            'starting_cash' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = CashierShift::create([
            'cashier_id' => $request->cashier_id,
            'starting_cash' => $request->starting_cash,
            'status' => 'open',
        ]);

        return response()->json(['message' => 'Cashier shift started successfully', 'shift' => $data], 201);
    }

    public function endShift(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'ending_cash' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $shift = CashierShift::find($id);
        if (!$shift || $shift->status !== 'open') {
            return response()->json(['message' => 'Invalid shift ID or shift already closed'], 404);
        }

        $shift->ending_cash = $request->ending_cash;
        $shift->status = 'closed';
        $shift->ended_at = now();
        $shift->save();

        return response()->json(['message' => 'Cashier shift ended successfully', 'shift' => $shift], 200);
    }

    public function getShift($id)
    {
        $shift = CashierShift::find($id);
        if (!$shift) {
            return response()->json(['message' => 'Shift not found'], 404);
        }
        return response()->json(['shift' => $shift], 200);
    }

    public function todayShifts()
    {
        $shifts = CashierShift::whereDate('created_at', now()->toDateString())->get();
        return response()->json(['shifts' => $shifts], 200);
    }

    public function allShifts()
    {
        $shifts = CashierShift::all();
        return response()->json(['shifts' => $shifts], 200);
    }

    public function orderPaymentMethods()
    {
        $methods = [
            'cash',
            'credit_card',
            'debit_card',
            'mobile_payment',
            'insurance',
        ];
        return response()->json(['payment_methods' => $methods], 200);
    }

    public function orderPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|string|in:cash,credit_card,debit_card,mobile_payment,insurance',
            'amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $order = Order::find($request->order_id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

       
        $order->payments()->create([
            'payment_method' => $request->payment_method,
            'amount_paid' => $request->amount,
            'payment_date' => now(),
            'transaction_reference' => uniqid('txn_'),
            'cashier_id' => $request->user()->id,
            'total_amount' => $order->total_amount,
        ]);

        return response()->json(['message' => 'Payment recorded successfully', 'payment' => $order->payments()->latest()->first()], 200);
    }
}
