<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user')->withCount('items')->latest();

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        if ($q = $request->get('q')) {
            $query->where(fn ($w) => $w->where('order_no', 'like', "%{$q}%")
                ->orWhere('phone', 'like', "%{$q}%")
                ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%")));
        }

        return view('admin.orders.index', [
            'orders' => $query->paginate(20)->withQueryString(),
            'totals' => [
                'all' => Order::count(),
                'paid' => Order::where('status', 'paid')->count(),
                'pending' => Order::where('status', 'pending')->count(),
                'revenue' => Order::where('status', 'paid')->sum('total'),
            ],
        ]);
    }

    public function show(Order $order)
    {
        return view('admin.orders.show', [
            'order' => $order->load(['items', 'payments', 'user']),
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate(['status' => 'required|in:pending,paid,failed,cancelled,refunded']);

        $order->update($data);

        if ($data['status'] === 'paid') {
            OrderService::fulfill($order);
        }

        return back()->with('success', 'Order marked as '.$data['status'].'.');
    }

    public function verifyPayment(Request $request, Payment $payment)
    {
        $action = $request->validate(['action' => 'required|in:verify,reject'])['action'];
        $payment->update(['status' => $action === 'verify' ? 'verified' : 'rejected']);

        if ($action === 'verify') {
            OrderService::fulfill($payment->order);
        } else {
            $payment->order->update(['status' => 'failed']);
        }

        return back()->with('success', 'Payment '.($action === 'verify' ? 'verified — access granted.' : 'rejected.'));
    }
}
