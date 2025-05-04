<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:owner,cashier');
    }

    public function create(Order $order): View
    {
        if ($order->status === 'cancelled') {
            return redirect()->route('orders.show', $order)->with('error', 'Cannot process payment for a cancelled order.');
        }

        if ($order->payment) {
            return redirect()->route('payments.show', $order->payment)->with('error', 'This order has already been paid.');
        }

        return view('payments.create', compact('order'));
    }

    public function store(Request $request, Order $order): RedirectResponse
    {
        if ($order->status === 'cancelled') {
            return redirect()->route('orders.show', $order)->with('error', 'Cannot process payment for a cancelled order.');
        }

        if ($order->payment) {
            return redirect()->route('payments.show', $order->payment)->with('error', 'This order has already been paid.');
        }

        $validated = $request->validate([
            'payment_method' => 'required|string|in:cash,card,transfer',
            'amount_paid' => 'required|numeric|min:' . $order->total_amount,
            'transaction_id' => 'nullable|required_if:payment_method,card,transfer|string',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method' => $validated['payment_method'],
                'amount_paid' => $validated['amount_paid'],
                'change_amount' => $validated['amount_paid'] - $order->total_amount,
                'transaction_id' => $validated['transaction_id'] ?? null,
                'user_id' => auth()->id(),
                'notes' => $validated['notes'] ?? null,
            ]);

            DB::commit();
            return redirect()->route('payments.show', $payment)->with('success', 'Payment processed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to process payment: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Payment $payment): View
    {
        $payment->load('order.items', 'order.tax', 'order.discount', 'order.table', 'user');
        return view('payments.show', compact('payment'));
    }

    public function printReceipt(Payment $payment): View
    {
        $payment->load('order.items', 'order.tax', 'order.discount', 'order.table', 'user');
        return view('payments.receipt', compact('payment'));
    }
}
