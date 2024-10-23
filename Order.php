<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\CartItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    public function index()
    {
        // Eager load the items and customer relationship
        $orders = Order::with(['items', 'customer'])

            // Running the count of items in the query
            ->withCount('items as items_count')
            // order by completed_at in descending order
            ->orderBy('completed_at', 'desc')

            ->get()

            // Transform the orders to include the total amount, items count, last added to cart, and if the order is completed
            ->map(function ($order) {
                $totalAmount = $order->items->sum(function ($item) {
                    return $item->price * $item->quantity;
                });

                $lastAddedToCart = $order
                    ->items
                    ->sortByDesc('created_at')
                    ->first()
                    ->created_at ?? null;



                return [
                    'order_id' => $order->id,
                    'customer_name' => $order->customer->name,
                    'total_amount' => $totalAmount,
                    'items_count' => $order->items_count,
                    'last_added_to_cart' => $lastAddedToCart,
                    'completed_order_exists' => $order->status == 'completed' ? true : false,
                    'completed_at' => $order->completed_at ?? null,
                    'created_at' => $order->created_at,
                ];
            });

        return view('orders.index', ['orders' => $orders]);
    }
}
