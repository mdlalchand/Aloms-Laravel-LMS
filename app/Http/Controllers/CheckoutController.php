<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\OrderService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index()
    {
        $totals = CartController::totals();
        if ($totals['count'] === 0) {
            return redirect()->route('cart.index')->with('info', 'Your cart is empty.');
        }
        return view('cart.checkout', $totals);
    }

    public function store(Request $request)
    {
        $totals = CartController::totals();
        abort_if($totals['count'] === 0, 403);

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:150',
            'address' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:255',
        ]);

        $order = Order::create($data + [
            'user_id' => $request->user()->id,
            'coupon_id' => $totals['coupon']?->id,
            'subtotal' => $totals['subtotal'],
            'discount' => $totals['discount'],
            'total' => $totals['total'],
            'status' => $totals['total'] <= 0 ? 'paid' : 'pending',
        ]);

        foreach ($totals['items'] as $item) {
            $order->items()->create([
                'item_type' => $item['type'],
                'item_id' => $item['id'],
                'title' => $item['title'],
                'price' => $item['price'],
                'qty' => 1,
            ]);
        }

        if ($totals['coupon']) {
            $totals['coupon']->increment('used_count');
        }

        if ($order->status === 'paid') {
            OrderService::fulfill($order);
        }

        session()->forget(['cart', 'coupon']);

        return redirect()->route('checkout.success', $order->order_no);
    }

    public function success(Order $order)
    {
        abort_unless($order->user_id === auth()->id() || auth()->user()->isAdmin(), 403);

        return view('cart.success', [
            'order' => $order->load('items', 'payments'),
            'paymentNumbers' => [
                'bkash' => \App\Models\Setting::get('bkash_number', '01700000000'),
                'nagad' => \App\Models\Setting::get('nagad_number', '01700000000'),
            ],
        ]);
    }

    /** Student submits a manual mobile-banking payment for verification. */
    public function pay(Request $request, Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $data = $request->validate([
            'gateway' => 'required|in:bkash,nagad,rocket,bank',
            'sender_number' => 'required|string|max:30',
            'transaction_id' => 'required|string|max:60',
        ]);

        Payment::create($data + [
            'order_id' => $order->id,
            'amount' => $order->total,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Payment submitted. We will verify it within a few minutes.');
    }
}
