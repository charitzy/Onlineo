<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;


class OrderController extends Controller
{
    public function index()
    {
        $order = Order::with('product:id,prod_name', 'user:id')->get(['id', 'user_id', 'product_id', 'order_status']);

        return response()->json(['order' => $order]);
    }

    public function store(Request $request)
    {
        // Assuming you have the user_id and product_id in the request
        $order = Order::create([
            'order_date' => now(),
            'order_status' => 'pending', // Set your default order status
            'user_id' => $request->user_id,
            'product_id' => $request->product_id,
        ]);

        return response()->json(['message' => 'Order created successfully', 'order' => $order]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }
}
