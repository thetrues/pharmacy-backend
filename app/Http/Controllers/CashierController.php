<?php

namespace App\Http\Controllers;

use App\Models\Order\Order;
use App\Models\CashierShift;
use App\Models\Sales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CashierController extends Controller
{
    public function startShift(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'startingCash' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();

        $data = CashierShift::create([
            'cashier_id' => $user->id,
            'starting_cash' => $request->startingCash,
            'status' => 'open',
        ]);

        return response()->json(['message' => 'Cashier shift started successfully', 'shift' => $data], 201);
    }

    public function endShift(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'actualCash' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $user = Auth::user();
        $shift = CashierShift::where('cashier_id', $user->id)->latest()->first();
        if (!$shift || $shift->status !== 'open') {
            return response()->json(['message' => 'Invalid shift ID or shift already closed'], 404);
        }

        $shift->ending_cash = $request->actualCash;
        $shift->status = 'closed';
         $shift->return_cash = $request->actualCash - $shift->starting_cash;
         $shift->shift_end = now();
        $shift->save();

        return response()->json(['message' => 'Cashier shift ended successfully', 'shift' => $shift], 200);
    }

    public function getShift(Request $request)
    {
        $user = Auth::user();
        $shift = CashierShift::where(['cashier_id' => $user->id, 'status' => 'open'])->latest()->first();
        if (!$shift) {
            return response()->json(['message' => 'No active shift found'], 404);
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

    public function getSales(){
        $sales = Sales::with('items')->get();

        $sales = $sales->map(function ($sale) {
            return [
                'id' => (int) $sale->id,
                'orderNumber' => (string) $sale->orderNumber,
                'customerId' => (int) $sale->customer_id,
                'customerName' => (string) $sale->customerName,
                'items' => $sale->items->map(function ($item) {
                    return [
                        'name' => $item->product->name,
                        'id' => (int) $item->product_id,
                        'quantity' => (int) $item->quantity,
                        'price' => (float) $item->unitPrice,
                    ];
                }),
                'subtotal' => (float) $sale->subtotalAmount,
                'tax' => (float) $sale->taxAmount,
                'total' => (float) $sale->totalAmount,
                'status' => isset($sale->status) ? (string) $sale->status : 'pending',
                'createdAt' => $sale->created_at->toDateTimeString(),
                'paidAt' => $sale->paid_at ? $sale->paid_at->toDateTimeString() : null,
            ];
        });

        return response()->json(['orders' => $sales], 200);
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

    public function salesPayment(Request $request)
    {
        /*{"cashAmount":2000,"cardAmount":0,"creditAmount":0,"change":704,"total":1296,"sale_id":1,"order_number":"ORD-20260214-0001","payment_method":"cash"}*/
        $validator = Validator::make($request->all(), [
            'sales_id' => 'required|exists:sales,id',
            'payment_method' => 'required|string|in:cash,credit_card,debit_card,mobile_payment,insurance',
            'total' => 'required|numeric|min:0',
            'cashAmount' => 'required|numeric|min:0',
            'cardAmount' => 'required|numeric|min:0',
            'creditAmount' => 'required|numeric|min:0',
            'change' => 'required|numeric|min:0',
            'order_number' => 'required|string|exists:sales,orderNumber|unique:order_payments,order_number',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $sales = Sales::find($request->sales_id);
        if (!$sales) {
            return response()->json(['message' => 'Sales not found'], 404);
        }

         $user = Auth::user();
         $shift = CashierShift::where(['cashier_id' => $user->id, 'status' => 'open'])->latest()->first();
            if (!$shift) {
                return response()->json(['message' => 'No active shift found for the cashier'], 404);
            }

        $amountPaid = $request->cashAmount + $request->cardAmount + $request->creditAmount;
        if ($amountPaid < $request->total) {
            return response()->json(['message' => 'Amount paid is less than total amount'], 400);
        }
       
        $sales->payments()->create([
            'payment_method' => $request->payment_method,
            'cash_amount' => $request->cashAmount,
            'card_amount' => $request->cardAmount,
            'credit_amount' => $request->creditAmount,
            'change' => $request->change,
            'amount_paid' => $amountPaid,
            'payment_date' => now(),
            'cashier_id' => $request->user()->id,
            'total_amount' => $request->total,
            'shift_id' => $shift->id,
            'order_number' => $request->order_number,
        ]);

            
            $sales->status = 'paid';
            $sales->save();

        return response()->json(['message' => 'Payment recorded successfully', 'payment' => $sales], 200);
    }
}
